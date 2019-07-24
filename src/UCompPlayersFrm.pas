unit UCompPlayersFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs,
  UBaseCheckFrm, FMX.ScrollBox, FMX.Memo, FMX.Objects, FMX.ListBox, FMX.Layouts,
  FMX.StdCtrls, FMX.Edit, FMX.Controls.Presentation;

type
  TCompPlayersFrm = class(TBaseCheckFrm)
    procedure bAddClick(Sender: TObject);
  private
  protected
    procedure OnClickButton(Sender: TObject); override;
    procedure ComparePlayers;
  public
    function SetCaption: string; override;
    function AcceptForm: Boolean; override;
    procedure AfterShow; override;
  end;

var
  CompPlayersFrm: TCompPlayersFrm;

implementation

uses
  System.IOUtils,
  UInterfaces, URESTMdl, uGenFunc, uPlayer;

{$R *.fmx}

function TCompPlayersFrm.AcceptForm: Boolean;
var
  Intf: IMainMenu;
begin
  Result := False;

  if lbID.Count <> 2 then
    Exit;

  if Supports(Owner, IMainMenu, Intf)  then
    Intf.ShowAni(True);

  mData.Lines.Clear;

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
                lSteps.Text := Format('Checking Player %s - try %d/10', [lbID.Items[i], j]);
              end);
            Mdl.LoadData(tcPlayer, lbID.Items[i]);
            Break;
          except
            Sleep(5000);
          end;
        end;
      end;

      TThread.Synchronize(TThread.CurrentThread,
        procedure
        begin
          ComparePlayers;
        end);
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

procedure TCompPlayersFrm.AfterShow;
begin
  inherited;
end;

procedure TCompPlayersFrm.bAddClick(Sender: TObject);
begin
  inherited;

  bAdd.Visible := lbID.Count <> 2;
end;

procedure TCompPlayersFrm.ComparePlayers;
type
  TPlayerI = record
    P: TPlayer;
    PlayerInfo: TPlayerInfo;
    ModsInfo: TModsInfo;
  end;
var
  PlayerI: array [0..1] of TPlayerI;
  i: Integer;
  L: TStringList;
begin
  for i := 0 to lbID.Count - 1 do
  begin
    PlayerI[i].P := nil;

    // carreguem player
    if TFile.Exists(lbID.Items[i] + '.json') then
    begin
      L := TStringList.Create;
      try
        L.LoadFromFile(lbID.Items[i] + '.json');
        PlayerI[i].P := TPlayer.FromJsonString(L.Text);
      finally
        FreeAndNil(L);
      end;
    end;

    if not Assigned(PlayerI[i].P) then
      Exit;

    PlayerI[i].ModsInfo := TGenFunc.CheckMods(PlayerI[i].P.Data.Ally_code.ToString);
    PlayerI[i].PlayerInfo := TGenFunc.CheckPlayer(PlayerI[i].P, FChar, PlayerI[i].ModsInfo, '');
  end;

  // mostrem dades generals
  mData.Lines.Clear;
  mData.Lines.Add(PlayerI[0].P.Data.Name + ' vs ' + PlayerI[1].P.Data.Name);
  mData.Lines.Add(PlayerI[0].P.Data.Guild_name + ' - ' + PlayerI[0].P.Data.Guild_name);
  mData.Lines.Add('');
  mData.Lines.Add('Power: ' + FormatFloat('#,##0', PlayerI[0].PlayerInfo.Power) + ' - ' + FormatFloat('#,##0', PlayerI[1].PlayerInfo.Power));
  mData.Lines.Add('GP: ' + FormatFloat('#,##0', PlayerI[0].P.Data.Galactic_power) + ' - ' + FormatFloat('#,##0', PlayerI[1].P.Data.Galactic_power));
  mData.Lines.Add('GP Char.: ' + FormatFloat('#,##0', PlayerI[0].P.Data.Character_galactic_power) + ' - ' + FormatFloat('#,##0', PlayerI[1].P.Data.Character_galactic_power));
  mData.Lines.Add('GP Ships: ' + FormatFloat('#,##0', PlayerI[0].P.Data.Ship_galactic_power) + ' - ' + FormatFloat('#,##0', PlayerI[1].P.Data.Ship_galactic_power));
  mData.Lines.Add('Gear12: ' + FormatFloat('#,##0', PlayerI[0].PlayerInfo.Gear12) + ' - ' + FormatFloat('#,##0', PlayerI[1].PlayerInfo.Gear12));
  mData.Lines.Add('Gear11: ' + FormatFloat('#,##0', PlayerI[0].PlayerInfo.Gear11) + ' - ' + FormatFloat('#,##0', PlayerI[1].PlayerInfo.Gear11));
  mData.Lines.Add('Gear10: ' + FormatFloat('#,##0', PlayerI[0].PlayerInfo.Gear10) + ' - ' + FormatFloat('#,##0', PlayerI[1].PlayerInfo.Gear10));
  mData.Lines.Add('Gear9: ' + FormatFloat('#,##0', PlayerI[0].PlayerInfo.Gear9) + ' - ' + FormatFloat('#,##0', PlayerI[1].PlayerInfo.Gear9));
  mData.Lines.Add('Gear8: ' + FormatFloat('#,##0', PlayerI[0].PlayerInfo.Gear8) + ' - ' + FormatFloat('#,##0', PlayerI[1].PlayerInfo.Gear8));
  mData.Lines.Add('Zetas: ' + FormatFloat('#,##0', PlayerI[0].PlayerInfo.Zetas) + ' - ' + FormatFloat('#,##0', PlayerI[1].PlayerInfo.Zetas));
  mData.Lines.Add('Char. Rank: ' + FormatFloat('#,##0', PlayerI[0].PlayerInfo.CharRank) + ' - ' + FormatFloat('#,##0', PlayerI[1].PlayerInfo.CharRank));
  mData.Lines.Add('Ships Rank: ' + FormatFloat('#,##0', PlayerI[0].PlayerInfo.ShipRank) + ' - ' + FormatFloat('#,##0', PlayerI[1].PlayerInfo.ShipRank));
  mData.Lines.Add('');
  mData.Lines.Add('Mods +20: ' + FormatFloat('#,##0', PlayerI[0].ModsInfo.Plus20) + ' - ' + FormatFloat('#,##0', PlayerI[1].ModsInfo.Plus20));
  mData.Lines.Add('Mods +15: ' + FormatFloat('#,##0', PlayerI[0].ModsInfo.Plus15) + ' - ' + FormatFloat('#,##0', PlayerI[1].ModsInfo.Plus15));
  mData.Lines.Add('Mods +10: ' + FormatFloat('#,##0', PlayerI[0].ModsInfo.Plus10) + ' - ' + FormatFloat('#,##0', PlayerI[1].ModsInfo.Plus10));
  mData.Lines.Add('Speed Arrows: ' + FormatFloat('#,##0', PlayerI[0].ModsInfo.Arrows) + ' - ' + FormatFloat('#,##0', PlayerI[1].ModsInfo.Arrows));
  mData.Lines.Add('Mods 6*: ' + FormatFloat('#,##0', PlayerI[0].ModsInfo.Mods6) + ' - ' + FormatFloat('#,##0', PlayerI[1].ModsInfo.Mods6));
end;

procedure TCompPlayersFrm.OnClickButton(Sender: TObject);
begin
  inherited;

  bAdd.Visible := lbID.Count <> 2;
end;

function TCompPlayersFrm.SetCaption: string;
begin
  Result := 'Compare two players';
end;

end.
