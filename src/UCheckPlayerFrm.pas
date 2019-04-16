unit UCheckPlayerFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.Edit,
  FMX.Controls.Presentation, FMX.StdCtrls, FMX.EditBox, FMX.NumberBox, FMX.ScrollBox,
  FMX.Memo,
  uInterfaces, uUnit, uAbilities, FMX.ListBox;

type
  TCheckPlayerFrm = class(TForm, IChildren)
    lPlayerID: TLabel;
    ePlayerId: TEdit;
    mData: TMemo;
    cbFormat: TComboBox;
    lFormat: TLabel;
    bToClbd: TButton;
    procedure bToClbdClick(Sender: TObject);
  private
    FChar: TUnitList;
    FShips: TUnitList;
    FAbi: TAbilities;

    procedure LoadUnitsFromFile;
    procedure CheckPlayer;
  public
    function SetCaption: string;
    function ShowOkButton: Boolean;
    function ShowBackButton: Boolean;
    function AcceptForm: Boolean;
    procedure AfterShow;
  end;

var
  CheckPlayerFrm: TCheckPlayerFrm;

implementation

uses
  System.IOUtils,
  uCharacter, uShips, uRESTMdl, uPlayer, uMods, uGenFunc;

{$R *.fmx}

{ TCheckPlayerFrm }

function TCheckPlayerFrm.AcceptForm: Boolean;
var
  Intf: IMainMenu;
begin
  Result := False;

  if ePlayerId.Text = '' then
    Exit;

  if Pos('http', ePlayerId.Text) <> 0 then
    ePlayerId.Text := TGenFunc.GetField(ePlayerId.Text, 5, '/');

  if Supports(Owner, IMainMenu, Intf)  then
    Intf.ShowAni(True);

  TThread.CreateAnonymousThread(procedure
  var
    Mdl: TRESTMdl;
  begin
    // realitzem procés
    Mdl := TRESTMdl.Create(nil);
    try
      Mdl.LoadData(tcPlayer, ePlayerId.Text);
      Mdl.LoadData(tcMods, ePlayerId.Text);
    finally
      FreeAndNil(Mdl);
    end;

    TThread.Synchronize(TThread.CurrentThread,
      procedure
      begin
        CheckPlayer;
      end);

    TThread.Synchronize(TThread.CurrentThread,
      procedure
      begin
        if Supports(Owner, IMainMenu, Intf)  then
          Intf.ShowAni(False);
      end);
  end
  ).Start;
end;

procedure TCheckPlayerFrm.AfterShow;
begin
  if cbFormat.ItemIndex = -1 then
    cbFormat.ItemIndex := 0;

  LoadUnitsFromFile;
end;

procedure TCheckPlayerFrm.bToClbdClick(Sender: TObject);
begin
  TGenFunc.CopyToClipboard(mData.Lines.Text);
end;

procedure TCheckPlayerFrm.CheckPlayer;
var
  L: TStringList;
  P: TPlayer;
  PlayerInfo: TPlayerInfo;
  ModsInfo: TModsInfo;
begin
  P := nil;

  // carreguem player
  if TFile.Exists(ePlayerId.Text + '.json') then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(ePlayerId.Text + '.json');
      P := TPlayer.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;
  end;

  if not Assigned(P) then
    Exit;

  PlayerInfo := TGenFunc.CheckPlayer(P, FChar);
  ModsInfo := TGenFunc.CheckMods(P.Data.Ally_code.ToString);

  // mostrem dades generals
  mData.Lines.Clear;
  case cbFormat.ItemIndex of
    0:
    begin
      mData.Lines.Add('Power: ' + FormatFloat('#,##0', PlayerInfo.Power));
      mData.Lines.Add('');
      mData.Lines.Add('GP: ' + FormatFloat('#,##0', P.Data.Galactic_power));
      mData.Lines.Add('GP Char.: ' + FormatFloat('#,##0', P.Data.Character_galactic_power));
      mData.Lines.Add('GP Ships: ' + FormatFloat('#,##0', P.Data.Ship_galactic_power));
      mData.Lines.Add('Guild: ' + P.Data.Guild_name);
      mData.Lines.Add('Name: ' + P.Data.Name);
      mData.Lines.Add('Gear12: ' + FormatFloat('#,##0', PlayerInfo.Gear12));
      mData.Lines.Add('Gear11: ' + FormatFloat('#,##0', PlayerInfo.Gear11));
      mData.Lines.Add('Gear10: ' + FormatFloat('#,##0', PlayerInfo.Gear10));
      mData.Lines.Add('Gear9: ' + FormatFloat('#,##0', PlayerInfo.Gear9));
      mData.Lines.Add('Gear8: ' + FormatFloat('#,##0', PlayerInfo.Gear8));
      mData.Lines.Add('Zetas: ' + FormatFloat('#,##0', PlayerInfo.Zetas));
      mData.Lines.Add('Char. Rank: ' + FormatFloat('#,##0', PlayerInfo.CharRank));
      mData.Lines.Add('Ships Rank: ' + FormatFloat('#,##0', PlayerInfo.ShipRank));
      mData.Lines.Add('');
      mData.Lines.Add('Mods +20: ' + FormatFloat('#,##0', ModsInfo.Plus20));
      mData.Lines.Add('Mods +15: ' + FormatFloat('#,##0', ModsInfo.Plus15));
      mData.Lines.Add('Mods +10: ' + FormatFloat('#,##0', ModsInfo.Plus10));
      mData.Lines.Add('Speed Arrows: ' + FormatFloat('#,##0', ModsInfo.Arrows));
      mData.Lines.Add('Mods 6*: ' + FormatFloat('#,##0', ModsInfo.Mods6));
    end;
    1:
      mData.Lines.Add(Format('"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s";"%s"', [
                                  P.Data.Name,
                                  P.Data.Ally_code.ToString,
                                  P.Data.Guild_name,
                                  FormatFloat('#,##0', PlayerInfo.Power),
                                  FormatFloat('#,##0', P.Data.Galactic_power),
                                  FormatFloat('#,##0', P.Data.Character_galactic_power),
                                  FormatFloat('#,##0', P.Data.Ship_galactic_power),
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
    2:
      mData.Lines.Add(Format('"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"'+ #9 + '"%s"', [
                                  P.Data.Name,
                                  P.Data.Ally_code.ToString,
                                  P.Data.Guild_name,
                                  FormatFloat('#,##0', PlayerInfo.Power),
                                  FormatFloat('#,##0', P.Data.Galactic_power),
                                  FormatFloat('#,##0', P.Data.Character_galactic_power),
                                  FormatFloat('#,##0', P.Data.Ship_galactic_power),
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

procedure TCheckPlayerFrm.LoadUnitsFromFile;
var
  L: TStringList;
begin
  // carreguem personatges
  if TFile.Exists(TGenFunc.GetBaseFolder + uCharacter.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(TGenFunc.GetBaseFolder + uCharacter.cFileName);
      FChar := TCharacters.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;
  end;

  // carreguem naus
  if TFile.Exists(TGenFunc.GetBaseFolder + uShips.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(TGenFunc.GetBaseFolder + uShips.cFileName);
      FShips := TShips.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;
  end;

  // carreguem habilitats
  if TFile.Exists(TGenFunc.GetBaseFolder + uAbilities.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(TGenFunc.GetBaseFolder + uAbilities.cFileName);
      FAbi := TAbilities.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;
  end;
end;

function TCheckPlayerFrm.SetCaption: string;
begin
  Result := '';
end;

function TCheckPlayerFrm.ShowBackButton: Boolean;
begin
  Result := True;
end;

function TCheckPlayerFrm.ShowOkButton: Boolean;
begin
  Result := True;
end;

end.
