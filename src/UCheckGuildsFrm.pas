unit UCheckGuildsFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.ScrollBox,
  FMX.Memo, FMX.Objects, FMX.ListBox, FMX.Layouts, FMX.StdCtrls, FMX.Edit,
  FMX.Controls.Presentation,
  UBaseCheckFrm, uPlayer, uGenFunc;

type
  TCheckGuildsFrm = class(TBaseCheckFrm)
    cbFormat: TComboBox;
    lFormat: TLabel;
  private
    FEndThread: Boolean;

    procedure OnTerminate(Sender: TObject);
    procedure CheckGuilds(AllyID: string);
    function GetInfoMods(Player: TPlayer): TModsInfo;
  public
    function SetCaption: string; override;
    function AcceptForm: Boolean; override;
    procedure AfterShow; override;
  end;

var
  CheckGuildsFrm: TCheckGuildsFrm;

implementation

uses
  System.IOUtils, System.DateUtils,
  UInterfaces, uTeams, uCharacter, uShips, uMessage, uRESTMdl, uGuild, uIniFiles;

{$R *.fmx}

{ TCheckAllyFrm }

function TCheckGuildsFrm.AcceptForm: Boolean;
var
  Intf: IMainMenu;
begin
  Result := False;

  TFileIni.SetFileIni(TGenFunc.GetIniName);
  TFileIni.SetSection('ALLY', lbID.Items);

  if lbID.Count = 0 then
    Exit;

  if Supports(Owner, IMainMenu, Intf)  then
    Intf.ShowAni(True);

  mData.Lines.Clear;

  if cbFormat.ItemIndex = 0 then
    mData.Lines.Add('"Player";"Ally Code";"Guild";"Power";"GP";"Char.GP";"Ship.GP";"Gear12";"Gear11";"Gear10";"Gear9";"Gear8";"Zetas";"Char.";"Ships";"Mods +20";"Mods +15";"Mods +10";"Arrows";"Mods 6*"')
  else
    mData.Lines.Add('"Player"'+ #9 + '"Ally Code"'+ #9 + '"Guild"'+ #9 + '"Power"'+ #9 + '"GP"'+ #9 + '"Char.GP"'+ #9 + '"Ship.GP"'+ #9 + '"Gear12"'+ #9 + '"Gear11"'+ #9 + '"Gear10"'+ #9 + '"Gear9"'+ #9 + '"Gear8"'+ #9 + '"Zetas"'+ #9 + '"Char."'+ #9 + '"Ships"'+ #9 + '"Mods +20"'+ #9 + '"Mods +15"'+ #9 + '"Mods +10"'+ #9 + '"Arrows"'+ #9 + '"Mods 6*"');

  TThread.CreateAnonymousThread(procedure
  var
    Mdl: TRESTMdl;
    i: Integer;
    j: Integer;
  begin
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
                lSteps.Text := Format('Checking Ally %s - try %d/10', [lbID.Items[i], j]);
              end);
            Mdl.LoadData(tcGuild, lbID.Items[i]);
            Break;
          except
            Sleep(5000);
          end;
        end;

        TThread.Synchronize(TThread.CurrentThread,
          procedure
          begin
            CheckGuilds(lbID.Items[i]);
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

procedure TCheckGuildsFrm.CheckGuilds(AllyID: string);
var
  i: Integer;
  L: TStringList;
  Guild: TGuild;
  PlayerInfo: TPlayerInfo;
  ModsInfo: TModsInfo;
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
    PlayerInfo := TGenFunc.CheckPlayer(Guild.Players[i], FChar);
    ModsInfo := GetInfoMods(Guild.Players[i]);

    if cbFormat.ItemIndex = 0 then
      mData.Lines.Add(Format('"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s"', [
                                  Guild.Players[i].Data.Name,
                                  Guild.Players[i].Data.Ally_code.ToString,
                                  Guild.Data.Name,
                                  FormatFloat('#,##0', PlayerInfo.Power),
                                  FormatFloat('#,##0', Guild.Players[i].Data.Galactic_power),
                                  FormatFloat('#,##0', Guild.Players[i].Data.Character_galactic_power),
                                  FormatFloat('#,##0', Guild.Players[i].Data.Ship_galactic_power),
                                  FormatFloat('#,##0', PlayerInfo.Gear12),
                                  FormatFloat('#,##0', PlayerInfo.Gear11),
                                  FormatFloat('#,##0', PlayerInfo.Gear10),
                                  FormatFloat('#,##0', PlayerInfo.Gear9),
                                  FormatFloat('#,##0', PlayerInfo.Gear8),
                                  FormatFloat('#,##0', PlayerInfo.Zetas),
                                  FormatFloat('#,##0', PlayerInfo.CharRank),
                                  FormatFloat('#,##0', PlayerInfo.ShipRank),
                                  FormatFloat('#,##0', ModsInfo.Plus20),
                                  FormatFloat('#,##0', ModsInfo.Plus15),
                                  FormatFloat('#,##0', ModsInfo.Plus10),
                                  FormatFloat('#,##0', ModsInfo.Arrows),
                                  FormatFloat('#,##0', ModsInfo.Mods6)
                                 ]))
    else
      mData.Lines.Add(Format('"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"', [
                                  Guild.Players[i].Data.Name,
                                  Guild.Players[i].Data.Ally_code.ToString,
                                  Guild.Data.Name,
                                  FormatFloat('#,##0', PlayerInfo.Power),
                                  FormatFloat('#,##0', Guild.Players[i].Data.Galactic_power),
                                  FormatFloat('#,##0', Guild.Players[i].Data.Character_galactic_power),
                                  FormatFloat('#,##0', Guild.Players[i].Data.Ship_galactic_power),
                                  FormatFloat('#,##0', PlayerInfo.Gear12),
                                  FormatFloat('#,##0', PlayerInfo.Gear11),
                                  FormatFloat('#,##0', PlayerInfo.Gear10),
                                  FormatFloat('#,##0', PlayerInfo.Gear9),
                                  FormatFloat('#,##0', PlayerInfo.Gear8),
                                  FormatFloat('#,##0', PlayerInfo.Zetas),
                                  FormatFloat('#,##0', PlayerInfo.CharRank),
                                  FormatFloat('#,##0', PlayerInfo.ShipRank),
                                  FormatFloat('#,##0', ModsInfo.Plus20),
                                  FormatFloat('#,##0', ModsInfo.Plus15),
                                  FormatFloat('#,##0', ModsInfo.Plus10),
                                  FormatFloat('#,##0', ModsInfo.Arrows),
                                  FormatFloat('#,##0', ModsInfo.Mods6)
                                 ]));
  end;
end;

function TCheckGuildsFrm.GetInfoMods(Player: TPlayer): TModsInfo;
var
  Th: TThread;
  FileName: string;
begin
  FileName := Player.Data.Ally_code.ToString + '_mods.json';

  // si no existeix o el fitxer de mods té més de 10 díes, el carreguem
  if not TFile.Exists(FileName) or
     (TFile.Exists(FileName) and (IncDay(TFile.GetLastWriteTime(FileName), 10) < Now)) then
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

procedure TCheckGuildsFrm.OnTerminate(Sender: TObject);
begin
  FEndThread := True;
end;

function TCheckGuildsFrm.SetCaption: string;
begin
  Result := 'Check Guilds';
end;

end.
