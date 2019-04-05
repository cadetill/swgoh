unit UCheckTeamsFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.Edit,
  FMX.StdCtrls, FMX.Controls.Presentation, FMX.ScrollBox, FMX.Memo,
  uInterfaces, uTeams, uGuild, uPlayer, FMX.ListBox, FMX.Layouts;

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
    procedure bToClbdClick(Sender: TObject);
  private
    FTeams: TTeams;

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
//  L: TStringList;
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

  // mirem si existeix Json de Teams
//  if not TFile.Exists(uTeams.cFileName) then
//  begin
//    FTeams := TTeams.Create;
//    Exit;
//  end;

  lSteps.Text := 'Loading Data....';

  TThread.CreateAnonymousThread(procedure
  var
    Mdl: TRESTMdl;
    FileName: string;
  begin
    // carreguem Json d'equips definits
//    L := TStringList.Create;
//    try
//      L.LoadFromFile(uTeams.cFileName);
//      FTeams := TTeams.FromJsonString(L.Text);
//    finally
//      FreeAndNil(L);
//    end;

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
  TGenFunc.CopyToClipboard(mData.Lines.Text);
end;

procedure TCheckTeamsFrm.CheckPlayerTeams(Player: TPlayer);
var
  i,j,k: Integer;
  Idx: Integer;
  TmpS: string;
  Line: string;
  TmpI: Integer;
  SumTeam: Integer;
  Fixed: array of Integer;
  NonFixed: array of Integer;
  Speed: string;
begin
  Line := Player.Data.Name;

  // recorrem els diferents equips
  for i := 0 to FTeams.Count do
  begin
    TmpS := '';
    SumTeam := 0;
    SetLength(Fixed, FTeams.Items[i].Count + 1);
    SetLength(NonFixed, FTeams.Items[i].Count + 1);
    for k := 0 to High(Fixed) do
    begin
      Fixed[k] := 0;
      NonFixed[k] := 0;
    end;

    // per cada un dels equips, recorrem les unitats
    for j := 0 to FTeams.Items[i].Count do
    begin
      TmpI := 0;
      Idx := Player.IndexOf(FTeams.Items[i].Units[j].Base_id);

      if Idx = -1 then
      begin
        if cbFormat.ItemIndex = 0 then
          TmpS := TmpS + ';0'
        else
          TmpS := TmpS + #9 + '0';
        Continue;
      end;

      // donem valor al gear
      case Player.Units[Idx].Data.Gear_level of
        12: TmpI := FTeams.Items[i].GetPointsG12;
        11: TmpI := FTeams.Items[i].GetPointsG11;
        10: TmpI := FTeams.Items[i].GetPointsG10;
        else TmpI := 0;
      end;

      // mirem les Zs
      for k := 0 to FTeams.Items[i].Units[j].Count do
      begin
        if Player.Units[Idx].Data.IndexOfZ(FTeams.Items[i].Units[j].Zetas[K]) <> -1 then
          TmpI := TmpI + FTeams.Items[i].GetPointsZeta;
      end;

      // mirem la velocitat
      if Player.Units[Idx].Data.Stats.S5 >= (FTeams.Items[i].Units[j].Speed - 5) then
        TmpI := TmpI + FTeams.Items[i].GetPointsSpeed;

      Speed := '';
      if FTeams.Items[i].Units[j].Speed > 0 then
        Speed := ' / ' + Player.Units[Idx].Data.Stats.S5.ToString;

      if cbFormat.ItemIndex = 0 then
        TmpS := TmpS + ';' + TmpI.ToString + Speed
      else
        TmpS := TmpS + #9 + TmpI.ToString + Speed;

      if FTeams.Items[i].Units[j].Fixed then
        Fixed[j] := TmpI
      else
        NonFixed[j] := TmpI;
    end;

    TGenFunc.QuickSort(Fixed, Low(Fixed), High(Fixed));
    TGenFunc.QuickSort(NonFixed, Low(NonFixed), High(NonFixed));

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
      Line := Line + ';' + SumTeam.ToString + TmpS
    else
      Line := Line + #9 + SumTeam.ToString + TmpS;
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
  Speed: string;
  Idx: Integer;
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

    lSteps.Text := 'Checking Team ' + FTeams.Items[i].Name;
    if TmpS <> '' then
    begin
      if cbFormat.ItemIndex = 0 then
        TmpS := TmpS + ';'
      else
        TmpS := TmpS + #9;
    end;
    TmpS := TmpS + FTeams.Items[i].Name + ' (' + FTeams.Items[i].Score.ToString + ')';

    // recorrem els toons de l'equip
    for j := 0 to FTeams.Items[i].Count do
    begin
      TmpI := FTeams.Items[i].GetPointsG12 + FTeams.Items[i].GetPointsSpeed;
      for k := 0 to FTeams.Items[i].Units[j].Count do
        TmpI := TmpI + FTeams.Items[i].GetPointsZeta;

      Speed := '';
      if FTeams.Items[i].Units[j].Speed > 0 then
        Speed := ' / ' + FTeams.Items[i].Units[j].Speed.ToString;

      if TmpS <> '' then
      begin
        if cbFormat.ItemIndex = 0 then
          TmpS := TmpS + ';'
        else
          TmpS := TmpS + #9;
      end;

      if FTeams.Items[i].Units[j].Fixed then
      begin
        if FTeams.Items[i].Units[j].Alias = '' then
          TmpS := TmpS + FTeams.Items[i].Units[j].Name + ' (' + TmpI.ToString + Speed + ')'
        else
          TmpS := TmpS + FTeams.Items[i].Units[j].Alias + ' (' + TmpI.ToString + Speed + ')';
      end
      else
      begin
        if FTeams.Items[i].Units[j].Alias = '' then
          TmpS := TmpS + '*' + FTeams.Items[i].Units[j].Name + ' (' + TmpI.ToString + Speed + ')'
        else
          TmpS := TmpS + '*' + FTeams.Items[i].Units[j].Alias + ' (' + TmpI.ToString + Speed + ')';
      end;
    end;
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
