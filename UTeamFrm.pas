unit UTeamFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs,
  FMX.Controls.Presentation, FMX.StdCtrls, FMX.Layouts, FMX.Edit, FMX.EditBox,
  FMX.NumberBox, FMX.ListBox, FMX.Objects,
  uTeams, uInterfaces, uUnit, uAbilities;

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
    procedure bAddUnitClick(Sender: TObject);
  private
    FChar: TUnitList;
    FShips: TUnitList;
    FAbi: TAbilities;
    FTeam: TTeam;

    procedure LoadUnitsFromFile;
    procedure OnClickButton(Sender: TObject);
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
  uCharacter, uShips, uMessage;

{$R *.fmx}

{ TTeamFrm }

function TTeamFrm.AcceptForm: Boolean;
begin
  Result := True;
end;

procedure TTeamFrm.AfterShow;
var
  i: Integer;
  lbItem: TListBoxItem;
begin
  LoadUnitsFromFile;
  lbZetas.Clear;
  lbUnits.Clear;

  if not Assigned(TagObject) then Exit;
  if not (TagObject is TTeam) then Exit;

  FTeam := TTeam(TagObject);

  eName.Text := FTeam.Name;
  for i := 0 to FTeam.Count do
  begin
    lbItem := TListBoxItem.Create(lbUnits);
    lbItem.Text := FTeam.Units[i].Name;
    lbItem.TagString := FTeam.Units[i].Base_id;
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

procedure TTeamFrm.LoadUnitsFromFile;
var
  L: TStringList;
  i: Integer;
begin
  cbUnits.Clear;

  // carreguem personatges
  if FileExists(uCharacter.cFileName) then
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
  if FileExists(uShips.cFileName) then
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
  if FileExists(uAbilities.cFileName) then
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
      FTeam.DeleteUnit(TListBoxItem(TButton(Sender).Owner).TagString);

      lbUnits.RemoveObject(TListBoxItem(TButton(Sender).Owner));
    end);
end;

function TTeamFrm.SetCaption: string;
begin
  Result := '';
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
