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
    pStars: TPanel;
    lStars: TLabel;
    eStars: TNumberBox;
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
    procedure bAddUnitClick(Sender: TObject);
    procedure eNameChange(Sender: TObject);
    procedure eGearChange(Sender: TObject);
    procedure eSpeedChange(Sender: TObject);
    procedure eStarsChange(Sender: TObject);
    procedure cbFixedChange(Sender: TObject);
    procedure lbZetasChangeCheck(Sender: TObject);
    procedure bScoreClick(Sender: TObject);
    procedure eScoreChange(Sender: TObject);
  private
    FChar: TUnitList;
    FShips: TUnitList;
    FAbi: TAbilities;
    FTeam: TTeam;

    procedure LoadUnitsFromFile;
    procedure OnClickButton(Sender: TObject);
    procedure ListBoxItemClick(Sender: TObject);
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
  uCharacter, uShips, uMessage;

{$R *.fmx}

{ TTeamFrm }

function TTeamFrm.AcceptForm: Boolean;
var
  i: Integer;
begin
  Result := True;

  // posem alias de les unitats
  for i := 0 to FTeam.Count do
    if FTeam.Units[i].Alias = '' then
      FTeam.Units[i].Alias := FChar.Items[FChar.IndexOf(FTeam.Units[i].Base_id)].Alias;

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

procedure TTeamFrm.bScoreClick(Sender: TObject);
begin
  TMessage.Show(FTeam.GetStringPoints);
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

procedure TTeamFrm.eNameChange(Sender: TObject);
begin
  FTeam.Name := eName.Text;
end;

procedure TTeamFrm.eScoreChange(Sender: TObject);
begin
  FTeam.Score := Trunc(eScore.Value);
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

procedure TTeamFrm.eStarsChange(Sender: TObject);
var
  Idx: Integer;
begin
  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FTeam.IndexOf(lbUnits.Selected.Text);
  if Idx = -1 then
    Exit;

  FTeam.Units[Idx].Stars := Trunc(eStars.Value);
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

  if TListBoxItem(Sender).IsChecked then
    FTeam.Units[Idx].AddZeta(lbZetas.Selected.TagString)
  else
    FTeam.Units[Idx].DeleteZeta(lbZetas.Selected.TagString);
end;

procedure TTeamFrm.ListBoxItemClick(Sender: TObject);
var
  Idx: Integer;
  Idx2: Integer;
  lbItem: TListBoxItem;
  i, j: Integer;
begin
  if not (Sender is TListBoxItem) then
    Exit;

  // carreguem Zs del personatge
  Idx := FChar.IndexOf(TListBoxItem(Sender).TagString);
  if Idx = -1 then
    Exit;

  Idx2 := 0;
  lbZetas.Clear;
  repeat
    Idx2 := FAbi.NextAbility(FChar.Items[Idx].Base_Id, Idx2);
    if (Idx2 <> -1) and FAbi.Items[Idx2].Is_zeta then
    begin
      lbItem := TListBoxItem.Create(lbZetas);
      lbItem.Text := FAbi.Items[Idx2].Name;
      lbItem.TagString := FAbi.Items[Idx2].Base_id;

      lbZetas.AddObject(lbItem);
    end;
  until Idx2 = -1;

  // carregem dades seleccionades prèviament
  Idx := FTeam.IndexOf(TListBoxItem(Sender).Text);
  if Idx = -1 then
    Exit;

  cbFixed.IsChecked := FTeam.Units[Idx].Fixed;
  eGear.Value := FTeam.Units[Idx].Gear;
  eSpeed.Value := FTeam.Units[Idx].Speed;
  eStars.Value := FTeam.Units[Idx].Stars;

  for i := 0 to FTeam.Units[Idx].Count do
    for j := 0 to lbZetas.Count - 1 do
      lbZetas.ListItems[j].IsChecked := FTeam.Units[Idx].IndexOf(lbZetas.ListItems[j].TagString) <> -1;
end;

procedure TTeamFrm.LoadUnitsFromFile;
var
  L: TStringList;
  i: Integer;
begin
  cbUnits.Clear;

  // carreguem personatges
  if TFile.Exists(uCharacter.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(uCharacter.cFileName);
      FChar := TCharacters.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;

    for i := 0 to FChar.Count do
      cbUnits.Items.AddObject(FChar.Items[i].Name, FChar.Items[i]);
  end;

  // carreguem naus
  if TFile.Exists(uShips.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(uShips.cFileName);
      FShips := TShips.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;

    for i := 0 to FShips.Count do
      cbUnits.Items.AddObject(FShips.Items[i].Name, FShips.Items[i]);
  end;

  // carreguem habilitats
  if TFile.Exists(uAbilities.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(uAbilities.cFileName);
      FAbi := TAbilities.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;
  end;
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
