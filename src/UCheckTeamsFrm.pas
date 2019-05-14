unit UCheckTeamsFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.Edit,
  FMX.StdCtrls, FMX.Controls.Presentation, FMX.ScrollBox, FMX.Memo,
  uInterfaces, uTeams, uGuild, uPlayer, FMX.ListBox, FMX.Layouts, FMX.TabControl;

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
  uRESTMdl, uGenFunc;

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
  TmpStr: string;
  Line: string;
  TmpI: Integer;
  SumTeam: Integer;
  SumFixed: Integer;
  Fixed: array of Integer;
  NonFixed: array of Integer;
  Speed: string;
  Comp: TComponent;
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

      if Idx1 = -1 then
      begin
        if cbFormat.ItemIndex = 0 then
          TmpS := TmpS + ';0'
        else
          TmpS := TmpS + #9 + '0';
        Continue;
      end;

      // donem valor al gear
      case Player.Units[Idx1].Data.Gear_level of
        12: TmpI := FTeams.Items[Idx].GetPointsG12;
        11: TmpI := FTeams.Items[Idx].GetPointsG11;
        10: TmpI := FTeams.Items[Idx].GetPointsG10;
        else TmpI := 0;
      end;

      // mirem les Zs
      for k := 0 to FTeams.Items[Idx].Units[j].Count do
      begin
        if Player.Units[Idx1].Data.IndexOfZ(FTeams.Items[Idx].Units[j].Zetas[K]) <> -1 then
          TmpI := TmpI + FTeams.Items[Idx].GetPointsZeta;
      end;

      // mirem la velocitat
      if (FTeams.Items[Idx].Units[j].Speed <> 0) and (Player.Units[Idx1].Data.Stats.S5 >= (FTeams.Items[Idx].Units[j].Speed - 5)) then
        TmpI := TmpI + FTeams.Items[Idx].GetPointsSpeed;

      Speed := '';
      if FTeams.Items[Idx].Units[j].Speed > 0 then
        Speed := ' / ' + Player.Units[Idx1].Data.Stats.S5.ToString;

      if cbFormat.ItemIndex = 0 then
        TmpS := TmpS + ';' + TmpI.ToString + Speed
      else
        TmpS := TmpS + #9 + TmpI.ToString + Speed;

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
      TmpStr := TmpStr + ';' + SumTeam.ToString + ';' + SumFixed.ToString + TmpS;
    end
    else
    begin
      Line := Line + #9 + SumTeam.ToString + #9 + SumFixed.ToString + TmpS;
      TmpStr := TmpStr + #9 + SumTeam.ToString + #9 + SumFixed.ToString + TmpS;
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
  TmpStr: string;
  Speed: string;
  Idx: Integer;
  SumFixed: Integer;
  Tab: TTabItem;
  Memo: TMemo;
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

    Tab := TTabItem.Create(tcData);
    Tab.Parent := tcData;
    Tab.Text := FTeams.Items[i].Name;
    Tab.Name := 't' + Idx.ToString;
    Tab.Tag := Idx;

    Memo := TMemo.Create(Tab);
    Memo.Parent := Tab;
    Memo.Align := TAlignLayout.Client;
    Memo.Text := '';
    Memo.Name := 'm' + Idx.ToString;

    lSteps.Text := 'Checking Team ' + FTeams.Items[Idx].Name;

    SumFixed := 0;
    TmpTeam := '';
    // recorrem els toons de l'equip
    for j := 0 to FTeams.Items[Idx].Count do
    begin
      TmpI := FTeams.Items[Idx].GetPointsG12;
      if FTeams.Items[Idx].Units[j].Speed > 0 then
        TmpI := TmpI + FTeams.Items[Idx].GetPointsSpeed;
      for k := 0 to FTeams.Items[Idx].Units[j].Count do
        TmpI := TmpI + FTeams.Items[Idx].GetPointsZeta;

      Speed := '';
      if FTeams.Items[Idx].Units[j].Speed > 0 then
        Speed := ' / ' + FTeams.Items[Idx].Units[j].Speed.ToString;

      if TmpTeam <> '' then
      begin
        if cbFormat.ItemIndex = 0 then
          TmpTeam := TmpTeam + ';'
        else
          TmpTeam := TmpTeam + #9;
      end;

      if FTeams.Items[Idx].Units[j].Fixed then
      begin
        SumFixed := SumFixed + TmpI;
        if FTeams.Items[Idx].Units[j].Alias = '' then
          TmpTeam := TmpTeam + FTeams.Items[Idx].Units[j].Name + ' (' + TmpI.ToString + Speed + ')'
        else
          TmpTeam := TmpTeam + FTeams.Items[Idx].Units[j].Alias + ' (' + TmpI.ToString + Speed + ')';
      end
      else
      begin
        if FTeams.Items[Idx].Units[j].Alias = '' then
          TmpTeam := TmpTeam + '*' + FTeams.Items[Idx].Units[j].Name + ' (' + TmpI.ToString + Speed + ')'
        else
          TmpTeam := TmpTeam + '*' + FTeams.Items[Idx].Units[j].Alias + ' (' + TmpI.ToString + Speed + ')';
      end;
    end;

    TmpStr := 'Player';

    if TmpS <> '' then
    begin
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
    end;
    TmpS := TmpS + FTeams.Items[Idx].Name + ' (' + FTeams.Items[Idx].Score.ToString + ')';
    TmpStr := TmpStr + FTeams.Items[Idx].Name + ' (' + FTeams.Items[Idx].Score.ToString + ')';

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
    TmpS := TmpS + TmpTeam;
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
