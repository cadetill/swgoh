unit UDefineTeamsFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.StdCtrls,
  FMX.Controls.Presentation, FMX.Edit, FMX.SearchBox, FMX.Layouts, FMX.ListBox,
  uTeams;

type
  TDefineTeamsFrm = class(TForm)
    lbTeams: TListBox;
    SearchBox1: TSearchBox;
    bAdd: TButton;
    ListBoxItem1: TListBoxItem;
    procedure bAddClick(Sender: TObject);
  private
    FTeams: TTeams;

    procedure GetDefinedTeams;
    procedure ListBoxItemClick(Sender: TObject);
    procedure OnClickButton(Sender: TObject);
  public
    constructor Create(AOwner: TComponent); override;
    destructor Destroy; override;
  end;

var
  DefineTeamsFrm: TDefineTeamsFrm;

implementation

uses
  uBase, uMessage, uInterfaces, UTeamFrm,
  FMX.DialogService;

{$R *.fmx}

{ TDefineTeamsFrm }

procedure TDefineTeamsFrm.bAddClick(Sender: TObject);
begin
  TDialogService.InputQuery('Set Name Team', ['Name'], [''],
    procedure(const AResult: TModalResult; const AValues: array of string)
    var
      lbItem: TListBoxItem;
      Team: TTeam;
      Items: TArray<TTeam>;
      Button: TButton;
    begin
      if (AResult = mrOk) and (AValues[0] <> '') then
      begin
        lbItem := TListBoxItem.Create(lbTeams);
        lbItem.Text := AValues[0];
        lbItem.ItemData.Detail := '';
        lbItem.ItemData.Accessory := TListBoxItemData.TAccessory.aDetail;
        lbItem.OnClick := ListBoxItemClick;

        Button := TButton.Create(lbItem);
        Button.Align := TAlignLayout.Right;
        Button.Width := 40;
        Button.StyleLookup := 'trashtoolbutton';
        Button.Parent := lbItem;
        Button.OnClick := OnClickButton;

        lbTeams.AddObject(lbItem);

        FTeams.AddTeam(AValues[0]);

        // guardem Json
        FTeams.SaveToFile(uTeams.cFileName);

        // executem OnClick
        ListBoxItemClick(lbItem);
      end;
    end);
end;

constructor TDefineTeamsFrm.Create(AOwner: TComponent);
begin
  inherited;

  GetDefinedTeams;
end;

destructor TDefineTeamsFrm.Destroy;
begin
  if Assigned(FTeams) then
    FreeAndNil(FTeams);

  inherited;
end;

procedure TDefineTeamsFrm.GetDefinedTeams;
var
  L: TStringList;
  lbItem: TListBoxItem;
  i: Integer;
  j: Integer;
  Button: TButton;
begin
  lbTeams.Clear;

  if Assigned(FTeams) then
    FreeAndNil(FTeams);

  if not FileExists(uTeams.cFileName) then
  begin
    FTeams := TTeams.Create;
    Exit;
  end;

  // carreguem Json
  L := TStringList.Create;
  try
    L.LoadFromFile(uTeams.cFileName);
    FTeams := TTeams.FromJsonString(L.Text);
  finally
    FreeAndNil(L);
  end;

  // creem TListBox
  for i := 0 to FTeams.Count do
  begin
    lbItem := TListBoxItem.Create(lbTeams);
    lbItem.Text := FTeams.Items[i].Name;
    lbItem.ItemData.Detail := '';
    for j := 0 to FTeams.Items[i].Count do
    begin
      if lbItem.ItemData.Detail <> '' then lbItem.ItemData.Detail := lbItem.ItemData.Detail + ' / ';
        lbItem.ItemData.Detail := lbItem.ItemData.Detail + FTeams.Items[i].Units[j].Name;
    end;
    lbItem.ItemData.Accessory := TListBoxItemData.TAccessory.aDetail;
    lbItem.OnClick := ListBoxItemClick;

    Button := TButton.Create(lbItem);
    Button.Align := TAlignLayout.Right;
    Button.Width := 40;
    Button.StyleLookup := 'trashtoolbutton';
    Button.Parent := lbItem;
    Button.OnClick := OnClickButton;

    lbTeams.AddObject(lbItem);
  end;
end;

procedure TDefineTeamsFrm.ListBoxItemClick(Sender: TObject);
var
  Intf: IMainMenu;
  Idx: Integer;
begin
  if not (Sender is TListBoxItem) then
    Exit;

  Idx := FTeams.IndexOf(TListBoxItem(Sender).Text);
  if Idx < 0 then
    Exit;

  // si es pot, creem formulari d'assistència
  if Supports(Owner, IMainMenu, Intf) then
    Intf.CreateForm(TTeamFrm, FTeams.Items[Idx]);
end;

procedure TDefineTeamsFrm.OnClickButton(Sender: TObject);
begin
  if not (Sender is TButton) then Exit;

  TMessage.MsjSiNo('Are you sure to want to delete the Team "%s"?', [TListBoxItem(TButton(Sender).Owner).Text],
    procedure
    begin
      FTeams.DeleteTeam(TListBoxItem(TButton(Sender).Owner).Text);
      FTeams.SaveToFile(uTeams.cFileName);

      lbTeams.RemoveObject(TListBoxItem(TButton(Sender).Owner));
    end);
end;

end.
