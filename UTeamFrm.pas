unit UTeamFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs,
  FMX.Controls.Presentation, FMX.StdCtrls, FMX.Layouts, FMX.Edit, FMX.EditBox,
  FMX.NumberBox, FMX.ListBox, FMX.Objects,
  uTeams, uInterfaces, uUnit, uAbilities, FMX.SearchBox;

type
  TTeamFrm = class(TForm, IChildren)
    lName: TLabel;
    eName: TEdit;
    cbUnits: TComboBox;
    pUnits: TPanel;
    bAddUnit: TButton;
    lbUnits: TListBox;
    lbZetas: TListBox;
    cbFixed: TCheckBox;
    pGear: TPanel;
    lGear: TLabel;
    eGear: TNumberBox;
    pSpeed: TPanel;
    lSpeed: TLabel;
    eSpeed: TNumberBox;
    pName: TPanel;
    ScrollBox1: TScrollBox;
    Line1: TLine;
    ListBoxItem1: TListBoxItem;
    ListBoxItem2: TListBoxItem;
    Line2: TLine;
    ListBoxItem3: TListBoxItem;
    Label1: TLabel;
    pScore: TPanel;
    lScore: TLabel;
    eScore: TNumberBox;
    bScore: TButton;
    cbDefTeam: TCheckBox;
    pHealth: TPanel;
    lHealth: TLabel;
    eHealth: TNumberBox;
    pFisDam: TPanel;
    lFisDam: TLabel;
    eFisDam: TNumberBox;
    pTenacity: TPanel;
    lTenacity: TLabel;
    eTenacity: TNumberBox;
    pSpeDam: TPanel;
    lSpeDam: TLabel;
    eSpeDam: TNumberBox;
    CheckBox1: TCheckBox;
    bCalc: TButton;
    pPG: TPanel;
    lPG: TLabel;
    ePG: TNumberBox;
    cbIsShip: TCheckBox;
    pPotency: TPanel;
    lPotency: TLabel;
    ePotency: TNumberBox;
    pCritChance: TPanel;
    lCritChance: TLabel;
    eCritChance: TNumberBox;
    pRelic: TPanel;
    lRelic: TLabel;
    eRelic: TNumberBox;
    pProtection: TPanel;
    lProtection: TLabel;
    eProtection: TNumberBox;
    pCritAvoid: TPanel;
    lCritAvoid: TLabel;
    eCritAvoid: TNumberBox;
    pCritDamage: TPanel;
    lCritDamage: TLabel;
    eCritDamage: TNumberBox;
    procedure bAddUnitClick(Sender: TObject);
    procedure eNameChange(Sender: TObject);
    procedure eGearChange(Sender: TObject);
    procedure eSpeedChange(Sender: TObject);
    procedure cbFixedChange(Sender: TObject);
    procedure lbZetasChangeCheck(Sender: TObject);
    procedure bScoreClick(Sender: TObject);
    procedure eScoreChange(Sender: TObject);
    procedure lbUnitsDragChange(SourceItem, DestItem: TListBoxItem;
      var Allow: Boolean);
    procedure cbDefTeamChange(Sender: TObject);
    procedure eHealthChange(Sender: TObject);
    procedure eTenacityChange(Sender: TObject);
    procedure eFisDamChange(Sender: TObject);
    procedure eSpeDamChange(Sender: TObject);
    procedure bCalcClick(Sender: TObject);
    procedure ePGChange(Sender: TObject);
    procedure cbIsShipChange(Sender: TObject);
    procedure ePotencyChange(Sender: TObject);
    procedure eCritChanceChange(Sender: TObject);
    procedure eRelicChange(Sender: TObject);
    procedure eProtectionChange(Sender: TObject);
    procedure eCritAvoidChange(Sender: TObject);
    procedure eCritDamageChange(Sender: TObject);
  private
    FChar: TUnitList;
    FShips: TUnitList;
    FAbi: TAbilities;
    FTeam: TTeam;

    procedure LoadUnitsFromFile;
    procedure OnClickButton(Sender: TObject);
    procedure ListBoxItemClick(Sender: TObject);
    procedure OnChangeChkZeta(Sender: TObject);
  public
    function SetCaption: string;
    function ShowOkButton: Boolean;
    function ShowBackButton: Boolean;
    function AcceptForm: Boolean;
    procedure AfterShow;
  end;

var
  TeamFrm: TTeamFrm;

implementation

uses
  System.IOUtils,
  uCharacter, uShips, uMessage, uGenFunc;

{$R *.fmx}

{ TTeamFrm }

function TTeamFrm.AcceptForm: Boolean;
var
  i: Integer;
  Idx: Integer;
begin
  Result := True;

  // actualitzem o posem alias de les unitats
  for i := 0 to FTeam.Count do
  begin
    Idx := FChar.IndexOf(FTeam.Units[i].Base_id);
    if Idx <> -1 then
      FTeam.Units[i].Alias := FChar.Items[Idx].Alias
    else
    begin
      Idx := FShips.IndexOf(FTeam.Units[i].Base_id);
      if Idx <> -1 then
        FTeam.Units[i].Alias := FShips.Items[Idx].Alias;
    end;
  end;

  if Assigned(FTeam.OnChange) then
    FTeam.OnChange(FTeam);
end;

procedure TTeamFrm.AfterShow;
var
  i: Integer;
  lbItem: TListBoxItem;
  Button: TButton;
begin
  LoadUnitsFromFile;
  lbZetas.Clear;
  lbUnits.Clear;

  if not Assigned(TagObject) then Exit;
  if not (TagObject is TTeam) then Exit;

  FTeam := TTeam(TagObject);

  eName.Text := FTeam.Name;
  eScore.Value := FTeam.Score;
  cbDefTeam.IsChecked := FTeam.DefTeam;
  cbIsShip.IsChecked := FTeam.IsShip;
  for i := 0 to FTeam.Count do
  begin
    lbItem := TListBoxItem.Create(lbUnits);
    lbItem.Text := FTeam.Units[i].Name;
    lbItem.TagString := FTeam.Units[i].Base_id;
    lbItem.OnClick := ListBoxItemClick;

    Button := TButton.Create(lbItem);
    Button.Align := TAlignLayout.Right;
    Button.Width := 40;
    Button.StyleLookup := 'trashtoolbutton';
    Button.Parent := lbItem;
    Button.OnClick := OnClickButton;

    lbUnits.AddObject(lbItem);
  end;
end;

procedure TTeamFrm.bAddUnitClick(Sender: TObject);
var
  lbItem: TListBoxItem;
  Button: TButton;
begin
  if cbUnits.ItemIndex = -1 then
    Exit;
  if lbUnits.Items.IndexOf(cbUnits.Items[cbUnits.ItemIndex]) <> -1 then
    Exit;

  lbItem := TListBoxItem.Create(lbUnits);
  lbItem.Text := cbUnits.Items[cbUnits.ItemIndex];
  lbItem.TagString := TUnit(cbUnits.Items.Objects[cbUnits.ItemIndex]).Base_Id;
  lbItem.OnClick := ListBoxItemClick;

  Button := TButton.Create(lbItem);
  Button.Align := TAlignLayout.Right;
  Button.Width := 40;
  Button.StyleLookup := 'trashtoolbutton';
  Button.Parent := lbItem;
  Button.OnClick := OnClickButton;

  lbUnits.AddObject(lbItem);

  FTeam.AddUnit(lbItem.TagString, lbItem.Text);

  cbUnits.ItemIndex := -1;
end;

procedure TTeamFrm.bCalcClick(Sender: TObject);
var
  i: Integer;
  Sum: Integer;
begin
  eScore.SetFocus;

  Sum := 0;
  for i := 0 to FTeam.Count do
    Sum := Sum + FTeam.Units[i].GetUnitScore;
  eScore.Value := Sum;
end;

procedure TTeamFrm.bScoreClick(Sender: TObject);
begin
  eScore.SetFocus;
  TMessage.Show(FTeam.GetStringPoints);
end;

procedure TTeamFrm.cbDefTeamChange(Sender: TObject);
begin
  FTeam.DefTeam := cbDefTeam.IsChecked;
end;

procedure TTeamFrm.cbFixedChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].Fixed := cbFixed.IsChecked;
end;

procedure TTeamFrm.cbIsShipChange(Sender: TObject);
begin
  FTeam.IsShip := cbIsShip.IsChecked;
end;

procedure TTeamFrm.eCritAvoidChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].CritAvoidance := Trunc(eCritAvoid.Value);
end;

procedure TTeamFrm.eCritChanceChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].CritChance := Trunc(eCritChance.Value);
end;

procedure TTeamFrm.eCritDamageChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].CritDamage := Trunc(eCritDamage.Value);
end;

procedure TTeamFrm.eFisDamChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].FisDam := Trunc(eFisDam.Value);
end;

procedure TTeamFrm.eGearChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].Gear := Trunc(eGear.Value);
end;

procedure TTeamFrm.eHealthChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].Health := Trunc(eHealth.Value);
end;

procedure TTeamFrm.eNameChange(Sender: TObject);
begin
  FTeam.Name := eName.Text;
end;

procedure TTeamFrm.ePGChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].PG := Trunc(ePG.Value);
end;

procedure TTeamFrm.ePotencyChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].Potency := Trunc(ePotency.Value);
end;

procedure TTeamFrm.eProtectionChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].Protection := Trunc(eProtection.Value);
end;

procedure TTeamFrm.eRelicChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].RelicTier := Trunc(eRelic.Value);
end;

procedure TTeamFrm.eScoreChange(Sender: TObject);
begin
  FTeam.Score := Trunc(eScore.Value);
end;

procedure TTeamFrm.eSpeDamChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].SpeDam := Trunc(eSpeDam.Value);
end;

procedure TTeamFrm.eSpeedChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].Speed := Trunc(eSpeed.Value);
end;

procedure TTeamFrm.eTenacityChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].Tenacity := Trunc(eTenacity.Value);
end;

procedure TTeamFrm.lbUnitsDragChange(SourceItem, DestItem: TListBoxItem;
  var Allow: Boolean);
begin
  Allow := FTeam.Move(SourceItem.Text, DestItem.Text);
end;

procedure TTeamFrm.lbZetasChangeCheck(Sender: TObject);
var
  Idx: Integer;
begin
  if not (Sender is TListBoxItem) then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  if Assigned(TListBoxItem(Sender).TagObject) and (TListBoxItem(Sender).TagObject is TCheckBox) then
    TCheckBox(TListBoxItem(Sender).TagObject).Visible := TListBoxItem(Sender).IsChecked;

  if not Assigned(lbZetas.Selected) then
    Exit;

  if TListBoxItem(Sender).IsChecked then
  begin
    if Assigned(TListBoxItem(Sender).TagObject) and (TListBoxItem(Sender).TagObject is TCheckBox) then
      FTeam.Units[Idx].AddZeta(lbZetas.Selected.TagString, TCheckBox(TListBoxItem(Sender).TagObject).IsChecked)
    else
      FTeam.Units[Idx].AddZeta(lbZetas.Selected.TagString, False); // per defecte obligatoria
  end
  else
    FTeam.Units[Idx].DeleteZeta(lbZetas.Selected.TagString);
end;

procedure TTeamFrm.ListBoxItemClick(Sender: TObject);
var
  Idx: Integer;
  Idx2: Integer;
  lbItem: TListBoxItem;
  i, j: Integer;
  Cbox: TCheckBox;
  List: TUnitList;
begin
  if not (Sender is TListBoxItem) then
    Exit;

  // carreguem Zs del personatge
  Idx := FChar.IndexOf(TListBoxItem(Sender).TagString);
  if Idx = -1 then
  begin
    Idx := FShips.IndexOf(TListBoxItem(Sender).TagString);
    if Idx = -1 then
      Exit
    else
      List := FShips;
  end
  else
    List := FChar;

  Idx2 := 0;
  lbZetas.Clear;
  repeat
    Idx2 := FAbi.NextAbility(List.Items[Idx].Base_Id, Idx2);
    if (Idx2 <> -1) and FAbi.Items[Idx2].Is_zeta then
    begin
      lbItem := TListBoxItem.Create(lbZetas);
      lbItem.Text := FAbi.Items[Idx2].Name;
      lbItem.TagString := FAbi.Items[Idx2].Base_id;

      lbZetas.AddObject(lbItem);

      Cbox := TCheckBox.Create(lbItem);
      Cbox.Parent := lbItem;
      Cbox.Text := 'Optional';
      Cbox.Align := TAlignLayout.Right;
      Cbox.Width := 90;
      Cbox.Visible := False;
      Cbox.OnChange := OnChangeChkZeta;
      lbItem.TagObject := Cbox;
    end;
  until Idx2 = -1;

  // carregem dades seleccionades prèviament
  Idx := FTeam.IndexOf(TListBoxItem(Sender).Text);
  if Idx = -1 then
    Exit;

  cbFixed.IsChecked := FTeam.Units[Idx].Fixed;
  ePG.Value := FTeam.Units[Idx].PG;
  eGear.Value := FTeam.Units[Idx].Gear;
  eSpeed.Value := FTeam.Units[Idx].Speed;
  eHealth.Value := FTeam.Units[Idx].Health;
  eProtection.Value := FTeam.Units[Idx].Protection;
  eTenacity.Value := FTeam.Units[Idx].Tenacity;
  ePotency.Value := FTeam.Units[Idx].Potency;
  eCritChance.Value := FTeam.Units[Idx].CritChance;
  eCritAvoid.Value := FTeam.Units[Idx].CritAvoidance;
  eCritDamage.Value := FTeam.Units[Idx].CritDamage;
  eFisDam.Value := FTeam.Units[Idx].FisDam;
  eSpeDam.Value := FTeam.Units[Idx].SpeDam;
  eRelic.Value := FTeam.Units[Idx].RelicTier;

  for i := 0 to FTeam.Units[Idx].Count do
    for j := 0 to lbZetas.Count - 1 do
    begin
      Idx2 := FTeam.Units[Idx].IndexOf(lbZetas.ListItems[j].TagString);
      lbZetas.ListItems[j].IsChecked := Idx2 <> -1;
      if lbZetas.ListItems[j].IsChecked and Assigned(lbZetas.ListItems[j].TagObject) and (lbZetas.ListItems[j].TagObject is TCheckBox) then
      begin
        TCheckBox(lbZetas.ListItems[j].TagObject).Visible := True;
        TCheckBox(lbZetas.ListItems[j].TagObject).IsChecked := FTeam.Units[Idx].Zetas[Idx2].Optional;
      end;
    end;
end;

procedure TTeamFrm.LoadUnitsFromFile;
var
  L: TStringList;
  i: Integer;
begin
  cbUnits.Clear;

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

    for i := 0 to FChar.Count do
      cbUnits.Items.AddObject(FChar.Items[i].Name, FChar.Items[i]);
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

    for i := 0 to FShips.Count do
      cbUnits.Items.AddObject(FShips.Items[i].Name, FShips.Items[i]);
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

procedure TTeamFrm.OnChangeChkZeta(Sender: TObject);
begin
  if not (Sender is TCheckBox) then
    Exit;

  if not (TCheckBox(Sender).Parent is TListBoxItem) then
    Exit;

  lbZetasChangeCheck(TListBoxItem(TCheckBox(Sender).Parent));
end;

procedure TTeamFrm.OnClickButton(Sender: TObject);
begin
  if not (Sender is TButton) then Exit;

  TMessage.MsjSiNo('Are you sure to want to delete the Item "%s"?', [TListBoxItem(TButton(Sender).Owner).Text],
    procedure
    begin
      FTeam.DeleteUnit(TListBoxItem(TButton(Sender).Owner).Text);

      lbUnits.RemoveObject(TListBoxItem(TButton(Sender).Owner));
    end);
end;

function TTeamFrm.SetCaption: string;
begin
  Result := 'Team Definition';
end;

function TTeamFrm.ShowBackButton: Boolean;
begin
  Result := True;
end;

function TTeamFrm.ShowOkButton: Boolean;
begin
  Result := True;
end;

end.
