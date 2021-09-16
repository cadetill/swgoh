unit UCheckGuildsFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.ScrollBox,
  FMX.Memo, FMX.Objects, FMX.ListBox, FMX.Layouts, FMX.StdCtrls, FMX.Edit,
  FMX.Controls.Presentation, FMX.Memo.Types, Data.DB, Datasnap.DBClient,

  UBaseCheckFrm, uPlayer, uGenFunc, uTeams;

type
  TCheckGuildsFrm = class(TBaseCheckFrm)
    cbFormat: TComboBox;
    lFormat: TLabel;
    cbCheckTeams: TCheckBox;
  private
    FEndThread: Boolean;
    FTeams: TTeams;

    procedure LoadTeams;
    procedure CreateDataSet;
    procedure OnTerminate(Sender: TObject);
    procedure CheckGuilds(AllyID, HTML: string);
    function CheckTeam(Player:TPlayer; Team: TTeam): Integer;
    function GetInfoMods(Player: TPlayer): TModsInfo;
  public
    constructor Create(AOwner: TComponent); override;
    destructor Destroy; override;

    function SetCaption: string; override;
    function AcceptForm: Boolean; override;
    procedure AfterShow; override;
  end;

var
  CheckGuildsFrm: TCheckGuildsFrm;

implementation

uses
  System.IOUtils, System.DateUtils,
  UInterfaces, uCharacter, uShips, uMessage, uRESTMdl, uGuild, uIniFiles;

{$R *.fmx}

{ TCheckAllyFrm }

function TCheckGuildsFrm.AcceptForm: Boolean;
var
  Intf: IMainMenu;
begin
  Result := False;

  if lbID.Count = 0 then
    Exit;

  if Supports(Owner, IMainMenu, Intf)  then
    Intf.ShowAni(True);

  mData.Lines.Clear;

  TThread.CreateAnonymousThread(procedure
  var
    Mdl: TRESTMdl;
    i: Integer;
    j: Integer;
    HTML: string;
  begin
    CreateDataSet;

    Mdl := TRESTMdl.Create(nil);
    try
      for i := 0 to lbID.Count - 1 do
      begin
        for j := 1 to 10 do
        begin
          try
            TThread.Synchronize(TThread.CurrentThread,
              procedure
              begin
                lSteps.Text := Format('Checking Ally %s - try %d/10', [lbID.ListItems[i].TagString, j]);
              end);
            Mdl.LoadData(tcGuild, lbID.ListItems[i].TagString);
            HTML := Mdl.LoadData(tcURL, lbID.ListItems[i].Text);

            Break;
          except
            Sleep(5000);
          end;
        end;

        TThread.Synchronize(TThread.CurrentThread,
          procedure
          begin
            CheckGuilds(lbID.ListItems[i].TagString, HTML);
          end);
      end;
    finally
      FreeAndNil(Mdl);
      lSteps.Text := '';

      TThread.Synchronize(TThread.CurrentThread,
        procedure
        begin
          if Supports(Owner, IMainMenu, Intf)  then
            Intf.ShowAni(False);
        end);
    end;
  end
  ).Start;
end;

procedure TCheckGuildsFrm.AfterShow;
var
  i: Integer;
  L: TStringList;
  lbItem: TListBoxItem;
  Button: TButton;
begin
  inherited;

  if cbFormat.ItemIndex = -1 then
    cbFormat.ItemIndex := 0;

  TFileIni.SetFileIni(TGenFunc.GetIniName);
  L := TStringList.Create;
  try
    TFileIni.GetSection('ALLY', L);
    for i := 0 to L.Count - 1 do
    begin
      lbItem := TListBoxItem.Create(lbID);
      lbItem.Text := L[i];

      Button := TButton.Create(lbItem);
      Button.Parent := lbItem;
      Button.Align := TAlignLayout.Right;
      Button.Size.Width := 40;
      Button.Size.Height := 30;
      Button.Size.PlatformDefault := False;
      Button.StyleLookup := 'trashtoolbutton';
      Button.Name := 'b' + L[i];
      Button.OnClick := OnClickButton;

      lbID.AddObject(lbItem);
    end;
  finally
    FreeAndNil(L);
  end;
end;

procedure TCheckGuildsFrm.CheckGuilds(AllyID, HTML: string);
var
  i,j: Integer;
  L: TStringList;
  Guild: TGuild;
  PlayerInfo: TPlayerInfo;
  ModsInfo: TModsInfo;
  TmpStr: string;
begin
  if not TFile.Exists(AllyID + '_guild.json') then
    Exit;

  L := TStringList.Create;
  try
    L.LoadFromFile(AllyID + '_guild.json');
    Guild := TGuild.FromJsonString(L.Text);
  finally
    FreeAndNil(L);
  end;

  // recorrem els Players de cada una de les Guilds
  for i := 0 to Guild.Count do
  begin
    cdsData.Append;
    cdsData.FieldByName('Player').AsString := Guild.Players[i].Data.Name;
    cdsData.FieldByName('AllyCode').AsInteger := Guild.Players[i].Data.Ally_code;
    cdsData.FieldByName('Url').AsString := 'https://swgoh.gg' + Guild.Players[i].Data.Url;
    cdsData.FieldByName('Guild').AsString := Guild.Data.Name;
    cdsData.FieldByName('GP').AsInteger := Guild.Players[i].Data.Galactic_power;
    cdsData.FieldByName('GPChar').AsInteger := Guild.Players[i].Data.Character_galactic_power;
    cdsData.FieldByName('GPShip').AsInteger := Guild.Players[i].Data.Ship_galactic_power;

    ModsInfo := GetInfoMods(Guild.Players[i]);
    cdsData.FieldByName('Mods25').AsInteger := ModsInfo.Plus25;
    cdsData.FieldByName('Mods20').AsInteger := ModsInfo.Plus20;
    cdsData.FieldByName('Mods15').AsInteger := ModsInfo.Plus15;
    cdsData.FieldByName('Mods10').AsInteger := ModsInfo.Plus10;
    cdsData.FieldByName('Mods6').AsInteger := ModsInfo.Mods6;

    PlayerInfo := TGenFunc.CheckPlayer(Guild.Players[i], FChar, ModsInfo, HTML);
    cdsData.FieldByName('Relics').AsInteger := PlayerInfo.Relics;
    cdsData.FieldByName('G13').AsInteger := PlayerInfo.Gear13;
    cdsData.FieldByName('G12').AsInteger := PlayerInfo.Gear12;
    cdsData.FieldByName('G11').AsInteger := PlayerInfo.Gear11;
    cdsData.FieldByName('G10').AsInteger := PlayerInfo.Gear10;
    cdsData.FieldByName('Zetas').AsInteger := PlayerInfo.Zetas;
    cdsData.FieldByName('ArenaChar').AsInteger := PlayerInfo.CharRank;
    cdsData.FieldByName('ArenaShips').AsInteger := PlayerInfo.ShipRank;
    cdsData.FieldByName('Cristals').AsInteger := PlayerInfo.Crystals;
    cdsData.FieldByName('GL').AsInteger := PlayerInfo.GL;

    if cbCheckTeams.IsChecked then
      for j := 0 to FTeams.Count do
        cdsData.FieldByName(FTeams.Items[j].Name).AsInteger := CheckTeam(Guild.Players[i], FTeams.Items[j]);

    cdsData.Post;
  end;

  cdsData.IndexName := 'player';
  cdsData.First;

  TmpStr := '';
  for i := 0 to cdsData.Fields.Count - 1 do
  begin
    if TmpStr <> '' then
    begin
      if cbFormat.ItemIndex = 0 then
        TmpStr := TmpStr + ';'
      else
        TmpStr := TmpStr + #9;
    end;

    TmpStr := TmpStr + '"' + cdsData.Fields[i].FieldName + '"';
  end;
  mData.Lines.Add(TmpStr);

  while not cdsData.Eof do
  begin
    TmpStr := '';
    for i := 0 to cdsData.Fields.Count - 1 do
    begin
      if TmpStr <> '' then
      begin
        if cbFormat.ItemIndex = 0 then
          TmpStr := TmpStr + ';'
        else
          TmpStr := TmpStr + #9;
      end;

      TmpStr := TmpStr + '"' + cdsData.Fields[i].AsString + '"';
    end;
    mData.Lines.Add(TmpStr);

    cdsData.Next;
  end;
end;

function TCheckGuildsFrm.CheckTeam(Player: TPlayer; Team: TTeam): Integer;
var
  i,j: Integer;
  Idx: Integer;
  IsOk: Boolean;
  CountZetas: Integer;
begin
  Result := 0;

  for i := 0 to Team.Count do
  begin
    Idx := Player.IndexOf(Team.Units[i].Base_id);
    if Idx = -1 then
      Continue;

    IsOk := True;

    // mirem PG
    if (Team.Units[i].PG <> 0) and (Player.Units[Idx].Data.Power < Team.Units[i].PG) then
      IsOk := False;

    // mirem les reliquies
    if (Team.Units[i].RelicTier <> 0) and
       ((Player.Units[Idx].Data.Gear_level <> 13) or ((Player.Units[Idx].Data.Gear_level = 13) and (Team.Units[i].RelicTier > (Player.Units[Idx].Data.Relic_tier - 2)))) then
      IsOk := False;

    // mirem gear
    if (Team.Units[i].Gear <> 0) and (Player.Units[Idx].Data.Gear_level < Team.Units[i].Gear) then
      IsOk := False;

    // mirem la velocitat
    if (Team.Units[i].Speed <> 0) and (Player.Units[Idx].Data.Stats.S5 < Team.Units[i].Speed) then
      IsOk := False;

    // mirem la Salud
    if (Team.Units[i].Health <> 0) and (Player.Units[Idx].Data.Stats.S1 < Team.Units[i].Health) then
      IsOk := False;

    // mirem la Protecció
    if (Team.Units[i].Protection <> 0) and (Player.Units[Idx].Data.Stats.S28 < Team.Units[i].Protection) then
      IsOk := False;

    // mirem la Tenacitat
    if (Team.Units[i].Tenacity <> 0) and ((Player.Units[Idx].Data.Stats.S18 * 100) < Team.Units[i].Tenacity) then
      IsOk := False;

    // mirem la dany físic
    if (Team.Units[i].FisDam <> 0) and (Player.Units[Idx].Data.Stats.S6 < Team.Units[i].FisDam) then
      IsOk := False;

    // mirem la dany especial
    if (Team.Units[i].SpeDam <> 0) and (Player.Units[Idx].Data.Stats.S7 < Team.Units[i].SpeDam) then
      IsOk := False;

    // mirem la Potència
    if (Team.Units[i].Potency <> 0) and ((Player.Units[Idx].Data.Stats.S17 * 100) < Team.Units[i].Potency) then
      IsOk := False;

    // mirem la probabilitat de crític
    if (Team.Units[i].CritChance <> 0) and (Player.Units[Idx].Data.Stats.S14 < Team.Units[i].CritChance) then
      IsOk := False;

    // mirem la evasió de crític
    if (Team.Units[i].CritAvoidance <> 0) and (Player.Units[Idx].Data.Stats.S39 < Team.Units[i].CritAvoidance) then
      IsOk := False;

    // mirem la dany crític
    if (Team.Units[i].CritDamage <> 0) and ((Player.Units[Idx].Data.Stats.S16 * 100) < Team.Units[i].CritDamage) then
      IsOk := False;

    // mirem zetes
    CountZetas := 0;
    for j := 0 to Team.Units[i].Count do
    begin
      if Player.Units[Idx].Data.IndexOfZ(Team.Units[i].Zetas[j].Base_id) <> -1 then
        Inc(CountZetas);
    end;
    if (Team.Units[i].Count <> -1) and (CountZetas <> Team.Units[i].Count+1) then
      IsOk := False;

    if IsOk then
      Inc(Result);
  end;
end;

constructor TCheckGuildsFrm.Create(AOwner: TComponent);
begin
  inherited;

  FTeams := TTeams.Create;
  LoadTeams;
end;

procedure TCheckGuildsFrm.CreateDataSet;
var
  i: Integer;
begin
  cdsData.Close;
  cdsData.FieldDefs.Clear;
  cdsData.FieldDefs.Add('Player', ftWideString, 50);
  cdsData.FieldDefs.Add('Url', ftWideString, 100);
  cdsData.FieldDefs.Add('AllyCode', ftInteger, 0);
  cdsData.FieldDefs.Add('Guild', ftWideString, 50);
  cdsData.FieldDefs.Add('Power', ftInteger, 0);
  cdsData.FieldDefs.Add('GP', ftInteger, 0);
  cdsData.FieldDefs.Add('GPChar', ftInteger, 0);
  cdsData.FieldDefs.Add('GPShip', ftInteger, 0);
  cdsData.FieldDefs.Add('Relics', ftInteger, 0);
  cdsData.FieldDefs.Add('G13', ftInteger, 0);
  cdsData.FieldDefs.Add('G12', ftInteger, 0);
  cdsData.FieldDefs.Add('G11', ftInteger, 0);
  cdsData.FieldDefs.Add('G10', ftInteger, 0);
  cdsData.FieldDefs.Add('Zetas', ftInteger, 0);
  cdsData.FieldDefs.Add('ArenaChar', ftInteger, 0);
  cdsData.FieldDefs.Add('ArenaShips', ftInteger, 0);
  cdsData.FieldDefs.Add('Mods25', ftInteger, 0);
  cdsData.FieldDefs.Add('Mods20', ftInteger, 0);
  cdsData.FieldDefs.Add('Mods15', ftInteger, 0);
  cdsData.FieldDefs.Add('Mods10', ftInteger, 0);
  cdsData.FieldDefs.Add('Mods6', ftInteger, 0);
  cdsData.FieldDefs.Add('Cristals', ftInteger, 0);
  cdsData.FieldDefs.Add('GL', ftInteger, 0);

  if cbCheckTeams.IsChecked then
  begin
    for i := 0 to FTeams.Count do
    begin
      cdsData.FieldDefs.Add(FTeams.Items[i].Name, ftInteger, 0);
    end;
  end;

  cdsData.IndexDefs.Clear;
  cdsData.IndexDefs.Add('player', 'Player', []);

  cdsData.CreateDataSet;
end;

destructor TCheckGuildsFrm.Destroy;
begin
  if Assigned(FTeams) then
    FreeAndNil(FTeams);

  inherited;
end;

function TCheckGuildsFrm.GetInfoMods(Player: TPlayer): TModsInfo;
var
  Th: TThread;
  FileName: string;
begin
  FileName := Player.Data.Ally_code.ToString + '_mods.json';

  // si no existeix o el fitxer de mods té més de 1 díes, el carreguem
  if not TFile.Exists(FileName) or
     (TFile.Exists(FileName) and (IncDay(TFile.GetLastWriteTime(FileName), 1) < Now)) then
  begin
    Th := TThread.CreateAnonymousThread(procedure
          var
            Mdl: TRESTMdl;
          begin
            // realitzem procés
            Mdl := TRESTMdl.Create(nil);
            try
              Mdl.LoadData(tcMods, Player.Data.Ally_code.ToString);
            finally
              FreeAndNil(Mdl);
            end;
            FEndThread := True;
          end
          );
    Th.OnTerminate := OnTerminate;
    FEndThread := False;
    Th.Start;
    repeat
      Sleep(5);
    until FEndThread;
  end;

  Result := TGenFunc.CheckMods(Player.Data.Ally_code.ToString);
end;

procedure TCheckGuildsFrm.LoadTeams;
var
  L: TStringList;
begin
  L := TStringList.Create;
  try
    L.LoadFromFile(TGenFunc.GetBaseFolder + uTeams.cFileName);
    FTeams := TTeams.FromJsonString(L.Text);
  finally
    FreeAndNil(L);
  end;
end;

procedure TCheckGuildsFrm.OnTerminate(Sender: TObject);
begin
  FEndThread := True;
end;

function TCheckGuildsFrm.SetCaption: string;
begin
  Result := 'Check Guilds';
end;

end.
