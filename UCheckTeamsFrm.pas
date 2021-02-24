unit UCheckTeamsFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.Edit,
  FMX.StdCtrls, FMX.Controls.Presentation, FMX.ScrollBox, FMX.Memo, FMX.ListBox,
  FMX.Layouts, FMX.TabControl,
  uInterfaces, uTeams, uPlayer, uUnit;

type
  TCheckTeamsFrm = class(TForm, IChildren)
    mData: TMemo;
    pName: TPanel;
    lName: TLabel;
    eName: TLabel;
    pChkGuild: TPanel;
    lChkGuild: TLabel;
    eChkGuild: TEdit;
    lbTeams: TListBox;
    ListBoxItem1: TListBoxItem;
    lSteps: TLabel;
    lFormat: TLabel;
    cbFormat: TComboBox;
    bToClbd: TButton;
    tcData: TTabControl;
    TabItem1: TTabItem;
    procedure bToClbdClick(Sender: TObject);
  private
    FTeams: TTeams;

    procedure DeleteAllTabs;
    procedure CheckTeams;
    procedure CheckPlayerTeams(Player: TPlayer);
  public
    constructor Create(AOwner: TComponent); override;
    destructor Destroy; override;

    function SetCaption: string;
    function ShowOkButton: Boolean;
    function ShowBackButton: Boolean;
    function AcceptForm: Boolean;
    procedure AfterShow;
  end;

var
  CheckTeamsFrm: TCheckTeamsFrm;

implementation

uses
  System.IOUtils, System.DateUtils, Generics.Collections,
  uRESTMdl, uGenFunc, uCharacter, uShips, uGuild;

{$R *.fmx}

{ TTeamCheck }

function TCheckTeamsFrm.AcceptForm: Boolean;
var
  Intf: IMainMenu;
begin
  Result := False;

  eName.Text := '';
  mData.Lines.Clear;

  if eChkGuild.Text = '' then
    Exit;

  if Pos('http', eChkGuild.Text) <> 0 then
    eChkGuild.Text := TGenFunc.GetField(eChkGuild.Text, 5, '/');

  // mostrem animació
  if Supports(Owner, IMainMenu, Intf)  then
    Intf.ShowAni(True);

  // esborrem totes les pestanyes excepte la primera
  DeleteAllTabs;

  lSteps.Text := 'Loading Data....';

  TThread.CreateAnonymousThread(procedure
  var
    Mdl: TRESTMdl;
    FileName: string;
  begin
    FileName := eChkGuild.Text + '_guild.json';
    eName.Text := eChkGuild.Text;

    // busquem dades de la Guild si no existeix o si l'arxiu té més de 24 hores
    if not TFile.Exists(FileName) or
       (TFile.Exists(FileName) and (IncHour(TFile.GetLastWriteTime(FileName), 24) < Now)) then
    begin
      Mdl := TRESTMdl.Create(nil);
      try
        Mdl.LoadData(tcGuild, eChkGuild.Text);
      finally
        FreeAndNil(Mdl);
      end;
    end;

    // Check de la Guild
    TThread.Synchronize(TThread.CurrentThread,
      procedure
      begin
        CheckTeams;
      end);

    // ocultem animació
    TThread.Synchronize(TThread.CurrentThread,
      procedure
      begin
        lSteps.Text := '';
        if Supports(Owner, IMainMenu, Intf) then
          Intf.ShowAni(False);
      end);
  end
  ).Start;
end;

procedure TCheckTeamsFrm.AfterShow;
begin
  lSteps.Text := '';
  eName.Text := '';
  cbFormat.ItemIndex := 1;
  TGenFunc.GetDefinedTeams(lbTeams, FTeams, nil, nil, nil);
end;

procedure TCheckTeamsFrm.bToClbdClick(Sender: TObject);
begin
  if tcData.ActiveTab.Index = 0 then
    TGenFunc.CopyToClipboard(mData.Lines.Text)
  else
    TGenFunc.CopyToClipboard(TMemo(tcData.ActiveTab.FindComponent('m' + tcData.ActiveTab.Tag.ToString)).Lines.Text);
end;

procedure TCheckTeamsFrm.CheckPlayerTeams(Player: TPlayer);
var
  i,j,k: Integer;
  Idx: Integer;
  Idx1: Integer;
  TmpS: string;
  TmpSS: string;
  TmpStr: string;
  Line: string;
  TmpI: Integer;
  SumTeam: Integer;
  SumFixed: Integer;
  Fixed: array of Integer;
  NonFixed: array of Integer;
  Stats: string;
  Comp: TComponent;
  CountZetas: Integer;
begin
  Line := Player.Data.Name;

  // recorrem tots els equips definits
  for i := 0 to lbTeams.Count - 1 do
  begin
    if not lbTeams.ItemByIndex(i).IsChecked then
      Continue;
    Idx := FTeams.IndexOf(lbTeams.ItemByIndex(i).Text);
    if Idx = -1 then
      Continue;

    TmpS := '';
    TmpSS := '';
    SumTeam := 0;
    SumFixed := 0;
    SetLength(Fixed, FTeams.Items[Idx].Count + 1);
    SetLength(NonFixed, FTeams.Items[Idx].Count + 1);
    for k := 0 to High(Fixed) do
    begin
      Fixed[k] := 0;
      NonFixed[k] := 0;
    end;

    // per cada un dels equips, recorrem les unitats
    for j := 0 to FTeams.Items[Idx].Count do
    begin
      TmpI := 0;
      Idx1 := Player.IndexOf(FTeams.Items[Idx].Units[j].Base_id);

      // no el té
      if Idx1 = -1 then
      begin
        if FTeams.Items[Idx].Units[j].Fixed then
          Fixed[j] := cMaxLevel * TTeam.GetPointsGearKo
        else
          NonFixed[j] := cMaxLevel * TTeam.GetPointsGearKo;

        if cbFormat.ItemIndex = 0 then
        begin
          TmpS := TmpS + ';' + (cMaxLevel * TTeam.GetPointsGearKo).ToString;
          TmpSS := TmpSS + ';' + (cMaxLevel * TTeam.GetPointsGearKo).ToString;
        end
        else
        begin
          TmpS := TmpS + #9 + (cMaxLevel * TTeam.GetPointsGearKo).ToString;
          TmpSS := TmpSS + #9 + (cMaxLevel * TTeam.GetPointsGearKo).ToString;
        end;
        Continue;
      end;

      Stats := '';
      // mirem el PG
      if FTeams.Items[Idx].Units[j].PG <> 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'pg' + Player.Units[Idx1].Data.Power.ToString;

        if Player.Units[Idx1].Data.Power >= FTeams.GetPercent(FTeams.Items[Idx].Units[j].PG) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsPG
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsPGKo;
      end;

      // mirem les reliquies
      if FTeams.Items[Idx].Units[j].RelicTier <> 0 then
      begin
        Player.Units[Idx1].Data.Relic_tier := Player.Units[Idx1].Data.Relic_tier - 2;
        if Player.Units[Idx1].Data.Relic_tier < 0 then
          Player.Units[Idx1].Data.Relic_tier := 0;

        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'r' + Player.Units[Idx1].Data.Relic_tier.ToString;

        if Player.Units[Idx1].Data.Relic_tier >= FTeams.GetPercent(FTeams.Items[Idx].Units[j].RelicTier) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsRelicTier
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsRelicTierKo;
      end;

      // donem valor al gear
      if FTeams.Items[Idx].Units[j].Gear = 0 then
      begin
        if FTeams.Items[Idx].IsShip then
          TmpI := TmpI + TTeam.GetPointsGear
        else
          TmpI := TmpI + cMaxLevel * TTeam.GetPointsGear;
      end
      else
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'g' + Player.Units[Idx1].Data.Gear_level.ToString;
        if Player.Units[Idx1].Data.Gear_level >= FTeams.Items[Idx].Units[j].Gear then
          TmpI := TmpI + ((Player.Units[Idx1].Data.Gear_level - FTeams.Items[Idx].Units[j].Gear) + 1) * TTeam.GetPointsGear
        else
          TmpI := TmpI + (FTeams.Items[Idx].Units[j].Gear - Player.Units[Idx1].Data.Gear_level) * TTeam.GetPointsGearKo;
      end;

      // mirem la velocitat
      if FTeams.Items[Idx].Units[j].Speed <> 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 's' + Player.Units[Idx1].Data.Stats.S5.ToString;

        if Player.Units[Idx1].Data.Stats.S5 >= (FTeams.Items[Idx].Units[j].Speed - 5) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsSpeed
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsSpeedKo;
      end;

      // mirem la Salud
      if FTeams.Items[Idx].Units[j].Health <> 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'h' + Player.Units[Idx1].Data.Stats.S1.ToString;

        if Player.Units[Idx1].Data.Stats.S1 >= FTeams.GetPercent(FTeams.Items[Idx].Units[j].Health) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsHealth
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsHealthKo;
      end;

      // mirem la Protecció
      if FTeams.Items[Idx].Units[j].Protection <> 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'pr' + Player.Units[Idx1].Data.Stats.S28.ToString;

        if Player.Units[Idx1].Data.Stats.S28 >= FTeams.GetPercent(FTeams.Items[Idx].Units[j].Protection) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsProtection
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsProtectionKo;
      end;

      // mirem la Tenacitat
      if FTeams.Items[Idx].Units[j].Tenacity <> 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 't' + Trunc(Player.Units[Idx1].Data.Stats.S18 * 100).ToString;

        if (Player.Units[Idx1].Data.Stats.S18 * 100) >= FTeams.GetPercent(FTeams.Items[Idx].Units[j].Tenacity) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsTenacity
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsTenacityKo;
      end;

      // mirem el dany físic
      if FTeams.Items[Idx].Units[j].FisDam <> 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'fd' + Player.Units[Idx1].Data.Stats.S6.ToString;

        if Player.Units[Idx1].Data.Stats.S6 >= FTeams.GetPercent(FTeams.Items[Idx].Units[j].FisDam) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsFDamage
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsFDamageKo;
      end;

      // mirem el dany especial
      if FTeams.Items[Idx].Units[j].SpeDam <> 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'sd' + Player.Units[Idx1].Data.Stats.S7.ToString;

        if Player.Units[Idx1].Data.Stats.S7 >= FTeams.GetPercent(FTeams.Items[Idx].Units[j].SpeDam) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsSDamage
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsSDamageKo;
      end;

      // mirem la Potència
      if FTeams.Items[Idx].Units[j].Potency <> 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'p' + Trunc(Player.Units[Idx1].Data.Stats.S17 * 100).ToString;

        if (Player.Units[Idx1].Data.Stats.S17 * 100) >= FTeams.GetPercent(FTeams.Items[Idx].Units[j].Potency) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsPotency
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsPotencyKo;
      end;

      // mirem la Prob. de crític
      if FTeams.Items[Idx].Units[j].CritChance <> 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'cc' + Trunc(Player.Units[Idx1].Data.Stats.S14).ToString;

        if Player.Units[Idx1].Data.Stats.S14 >= FTeams.GetPercent(FTeams.Items[Idx].Units[j].CritChance) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsCritChance
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsCritChanceKo;
      end;

      // mirem la Evasió de crític
      if FTeams.Items[Idx].Units[j].CritAvoidance <> 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'ca' + Trunc(Player.Units[Idx1].Data.Stats.S39).ToString;

        if Player.Units[Idx1].Data.Stats.S39 >= FTeams.GetPercent(FTeams.Items[Idx].Units[j].CritAvoidance) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsCritChance
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsCritChanceKo;
      end;

      // mirem la Dany crític
      if FTeams.Items[Idx].Units[j].CritDamage <> 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'cd' + Trunc(Player.Units[Idx1].Data.Stats.S16 * 100).ToString;

        if (Player.Units[Idx1].Data.Stats.S16 * 100) >= FTeams.GetPercent(FTeams.Items[Idx].Units[j].CritDamage) then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsCritChance
        else
          TmpI := TmpI + FTeams.Items[Idx].GetPointsCritChanceKo;
      end;

      // mirem les Zs
      CountZetas := 0;
      for k := 0 to FTeams.Items[Idx].Units[j].Count do
      begin
        if Player.Units[Idx1].Data.IndexOfZ(FTeams.Items[Idx].Units[j].Zetas[K].Base_id) <> -1 then
        begin
          Inc(CountZetas);
          TmpI := TmpI + FTeams.Items[Idx].GetPointsZeta;
        end
        else
          if not FTeams.Items[Idx].Units[j].Zetas[K].Optional then
            TmpI := TmpI + FTeams.Items[Idx].GetPointsZetaKo;
      end;
      if CountZetas > 0 then
      begin
        if Stats <> '' then  Stats := Stats + '|';
        Stats := Stats + 'z' + CountZetas.ToString;
      end;

      if Stats <> '' then
        Stats := ' / ' + Stats;

      // donem format escollit
      if cbFormat.ItemIndex = 0 then
      begin
        TmpS := TmpS + ';' + TmpI.ToString;
        TmpSS := TmpSS + ';' + TmpI.ToString + Stats;
      end
      else
      begin
        TmpS := TmpS + #9 + TmpI.ToString;
        TmpSS := TmpSS + #9 + TmpI.ToString + Stats;
      end;

      if FTeams.Items[Idx].Units[j].Fixed then
        Fixed[j] := TmpI
      else
        NonFixed[j] := TmpI;
    end;

    TGenFunc.QuickSort(Fixed, Low(Fixed), High(Fixed));
    TGenFunc.QuickSort(NonFixed, Low(NonFixed), High(NonFixed));

    TmpStr := Player.Data.Name;

    TmpI := 0;
    for j := High(Fixed) downto 0 do
    begin
      if Fixed[j] <> 0 then
      begin
        SumTeam := SumTeam + Fixed[j];
        Inc(TmpI);
      end;
      if TmpI = 5 then
        Break;
    end;
    SumFixed := SumTeam;

    for j := High(NonFixed) downto 0 do
    begin
      if TmpI = 5 then
        Break;
      if NonFixed[j] <> 0 then
      begin
        SumTeam := SumTeam + NonFixed[j];
        Inc(TmpI);
      end;
    end;
    if cbFormat.ItemIndex = 0 then
    begin
      Line := Line + ';' + SumTeam.ToString + ';' + SumFixed.ToString + TmpS;
      TmpStr := TmpStr + ';' + FormatFloat('#,##0.00', (SumTeam * 100) / FTeams.Items[Idx].GetMaxScore) + ';' + SumTeam.ToString + ';' + SumFixed.ToString + TmpSS;
    end
    else
    begin
      Line := Line + #9 + SumTeam.ToString + #9 + SumFixed.ToString + TmpS;
      TmpStr := TmpStr + #9 + FormatFloat('#,##0.00', (SumTeam * 100) / FTeams.Items[Idx].GetMaxScore) + #9 + SumTeam.ToString + #9 + SumFixed.ToString + TmpSS;
    end;

    Comp := tcData.FindComponent('t' + Idx.ToString).FindComponent('m' + Idx.ToString);
    if Comp is TMemo then
      TMemo(Comp).Lines.Add(TmpStr);
  end;
  mData.Lines.Add(Line);
end;

procedure TCheckTeamsFrm.CheckTeams;
var
  Guild: TGuild;
  L: TStringList;
  i,j,k: Integer;
  TmpS: string;
  TmpI: Integer;
  TmpTeam: string;
  TmpTeam2: string;
  TmpStr: string;
  Stats: string;
  Idx: Integer;
  SumFixed: Integer;
  Tab: TTabItem;
  Memo: TMemo;
  CountZetas: Integer;
begin
  if not TFile.Exists(eChkGuild.Text + '_guild.json') then
    Exit;

  L := TStringList.Create;
  try
    L.LoadFromFile(eChkGuild.Text + '_guild.json');
    Guild := TGuild.FromJsonString(L.Text);
  finally
    FreeAndNil(L);
  end;

  eName.Text := Guild.Data.Name;

  TmpS := 'Player';
  // recorrem tots els equips definits
  for i := 0 to lbTeams.Count - 1 do
  begin
    if not lbTeams.ItemByIndex(i).IsChecked then
      Continue;
    Idx := FTeams.IndexOf(lbTeams.ItemByIndex(i).Text);
    if Idx = -1 then
      Continue;

    // creem Tab del nou equip
    Tab := TTabItem.Create(tcData);
    Tab.Parent := tcData;
    Tab.Text := FTeams.Items[Idx].Name;
    Tab.Name := 't' + Idx.ToString;
    Tab.Tag := Idx;

    Memo := TMemo.Create(Tab);
    Memo.Parent := Tab;
    Memo.Align := TAlignLayout.Client;
    Memo.Text := '';
    Memo.Name := 'm' + Idx.ToString;

    // mostrem Team que s'està tractant
    lSteps.Text := 'Checking Team ' + FTeams.Items[Idx].Name;

    SumFixed := 0;
    TmpTeam := '';
    TmpTeam2 := '';
    // recorrem els toons de l'equip
    for j := 0 to FTeams.Items[Idx].Count do
    begin
      TmpI := FTeams.Items[Idx].Units[j].GetUnitScore;

      // control del Stats
      Stats := '';
      if FTeams.Items[Idx].Units[j].PG > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'pg' + FTeams.Items[Idx].Units[j].PG.ToString;
      end;
      if FTeams.Items[Idx].Units[j].RelicTier > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'r' + FTeams.Items[Idx].Units[j].RelicTier.ToString;
      end;
      if FTeams.Items[Idx].Units[j].Gear > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'g' + FTeams.Items[Idx].Units[j].Gear.ToString;
      end;
      if FTeams.Items[Idx].Units[j].Speed > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 's' + FTeams.Items[Idx].Units[j].Speed.ToString;
      end;
      if FTeams.Items[Idx].Units[j].Health > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'h' + FTeams.Items[Idx].Units[j].Health.ToString;
      end;
      if FTeams.Items[Idx].Units[j].Protection > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'pr' + FTeams.Items[Idx].Units[j].Protection.ToString;
      end;
      if FTeams.Items[Idx].Units[j].Tenacity > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 't' + FTeams.Items[Idx].Units[j].Tenacity.ToString;
      end;
      if FTeams.Items[Idx].Units[j].FisDam > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'fd' + FTeams.Items[Idx].Units[j].FisDam.ToString;
      end;
      if FTeams.Items[Idx].Units[j].SpeDam > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'sd' + FTeams.Items[Idx].Units[j].SpeDam.ToString;
      end;
      if FTeams.Items[Idx].Units[j].Potency > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'p' + FTeams.Items[Idx].Units[j].Potency.ToString;
      end;
      if FTeams.Items[Idx].Units[j].CritChance > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'cc' + FTeams.Items[Idx].Units[j].CritChance.ToString;
      end;
      if FTeams.Items[Idx].Units[j].CritAvoidance > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'ca' + FTeams.Items[Idx].Units[j].CritAvoidance.ToString;
      end;
      if FTeams.Items[Idx].Units[j].CritDamage > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'cd' + FTeams.Items[Idx].Units[j].CritDamage.ToString;
      end;
      CountZetas := 0;
      for k := 0 to FTeams.Items[Idx].Units[j].Count do
        if not FTeams.Items[Idx].Units[j].Zetas[k].Optional then
          Inc(CountZetas);
      if CountZetas > 0 then
      begin
        if Stats <> '' then Stats := Stats + '|';
        Stats := Stats + 'z' + CountZetas.ToString;
      end;
      if Stats <> '' then
        Stats := ' / ' + Stats;

      if TmpTeam <> '' then
      begin
        if cbFormat.ItemIndex = 0 then
        begin
          TmpTeam := TmpTeam + ';';
          TmpTeam2 := TmpTeam2 + ';';
        end
        else
        begin
          TmpTeam := TmpTeam + #9;
          TmpTeam2 := TmpTeam2 + #9;
        end;
      end;

      if FTeams.Items[Idx].Units[j].Fixed then
      begin
        SumFixed := SumFixed + TmpI;
        if FTeams.Items[Idx].Units[j].Alias = '' then
        begin
          TmpTeam := TmpTeam + FTeams.Items[Idx].Units[j].Name + ' (' + TmpI.ToString + Stats + ')';
          TmpTeam2 := TmpTeam2 + FTeams.Items[Idx].Units[j].Name + ' (' + TmpI.ToString + ')';
        end
        else
        begin
          TmpTeam := TmpTeam + FTeams.Items[Idx].Units[j].Alias + ' (' + TmpI.ToString + Stats + ')';
          TmpTeam2 := TmpTeam2 + FTeams.Items[Idx].Units[j].Alias + ' (' + TmpI.ToString + ')';
        end;
      end
      else
      begin
        if FTeams.Items[Idx].Units[j].Alias = '' then
        begin
          TmpTeam := TmpTeam + '*' + FTeams.Items[Idx].Units[j].Name + ' (' + TmpI.ToString + Stats + ')';
          TmpTeam2 := TmpTeam2 + '*' + FTeams.Items[Idx].Units[j].Name + ' (' + TmpI.ToString + ')';
        end
        else
        begin
          TmpTeam := TmpTeam + '*' + FTeams.Items[Idx].Units[j].Alias + ' (' + TmpI.ToString + Stats + ')';
          TmpTeam2 := TmpTeam2 + '*' + FTeams.Items[Idx].Units[j].Alias + ' (' + TmpI.ToString + ')';
        end;
      end;
    end;

    TmpStr := 'Player';

    if TmpS <> '' then
    begin
      if cbFormat.ItemIndex = 0 then
      begin
        TmpS := TmpS + ';';
        TmpStr := TmpStr + ';%;';
      end
      else
      begin
        TmpS := TmpS + #9;
        TmpStr := TmpStr + #9 + '%' + #9;
      end;
    end;
    TmpS := TmpS + FTeams.Items[Idx].Name + ' (' + FTeams.Items[Idx].Score.ToString + '/' + FTeams.Items[Idx].GetMaxScore.ToString + ')';
    TmpStr := TmpStr + FTeams.Items[Idx].Name + ' (' + FTeams.Items[Idx].Score.ToString + '/' + FTeams.Items[Idx].GetMaxScore.ToString + ')';

    if cbFormat.ItemIndex = 0 then
    begin
      TmpS := TmpS + ';';
      TmpStr := TmpStr + ';';
    end
    else
    begin
      TmpS := TmpS + #9;
      TmpStr := TmpStr + #9;
    end;
    TmpS := TmpS + 'F.(' + SumFixed.ToString + ')';
    TmpStr := TmpStr + 'F.(' + SumFixed.ToString + ')';
    if cbFormat.ItemIndex = 0 then
    begin
      TmpS := TmpS + ';';
      TmpStr := TmpStr + ';';
    end
    else
    begin
      TmpS := TmpS + #9;
      TmpStr := TmpStr + #9;
    end;
    TmpS := TmpS + TmpTeam2;
    Memo.Lines.Add(TmpStr + TmpTeam);
  end;

  mData.Lines.Add(TmpS);

  // loop pels players, i per cada un d'ells, mirem els teams
  for i := 0 to Guild.Count do
    CheckPlayerTeams(Guild.Players[i]);
end;

constructor TCheckTeamsFrm.Create(AOwner: TComponent);
begin
  inherited;

  FTeams := TTeams.Create;
end;

procedure TCheckTeamsFrm.DeleteAllTabs;
var
  i: Integer;
begin
  for i := tcData.TabCount - 1 downto 1 do
    tcData.Tabs[i].Free;
end;

destructor TCheckTeamsFrm.Destroy;
begin
  if Assigned(FTeams) then
    FreeAndNil(FTeams);

  inherited;
end;

function TCheckTeamsFrm.SetCaption: string;
begin
  Result := 'Check Teams';
end;

function TCheckTeamsFrm.ShowBackButton: Boolean;
begin
  Result := True;
end;

function TCheckTeamsFrm.ShowOkButton: Boolean;
begin
  Result := True;
end;

end.
