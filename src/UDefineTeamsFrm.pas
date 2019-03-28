unit UDefineTeamsFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.StdCtrls,
  FMX.Controls.Presentation, FMX.Edit, FMX.SearchBox, FMX.Layouts, FMX.ListBox,
  uTeams, uInterfaces;

type
  TDefineTeamsFrm = class(TForm, IChildren)
    lbTeams: TListBox;
    SearchBox1: TSearchBox;
    bAdd: TButton;
    ListBoxItem1: TListBoxItem;
    procedure bAddClick(Sender: TObject);
  private
    FTeams: TTeams;

    procedure OnChangeTeam(Sender: TObject);

    procedure ListBoxItemClick(Sender: TObject);
    procedure OnClickButton(Sender: TObject);
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
  DefineTeamsFrm: TDefineTeamsFrm;

implementation

uses
  uBase, uMessage, UTeamFrm, uGenFunc, UCheckTeamsFrm,
  FMX.DialogService, System.IOUtils;

{$R *.fmx}

{ TDefineTeamsFrm }

function TDefineTeamsFrm.AcceptForm: Boolean;
begin
  Result := True;
end;

procedure TDefineTeamsFrm.AfterShow;
begin
  TGenFunc.GetDefinedTeams(lbTeams, FTeams, OnChangeTeam, ListBoxItemClick, OnClickButton);
end;

procedure TDefineTeamsFrm.bAddClick(Sender: TObject);
begin
  TDialogService.InputQuery('Set Name Team', ['Name'], [''],
    procedure(const AResult: TModalResult; const AValues: array of string)
    var
      lbItem: TListBoxItem;
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

        FTeams.AddTeam(AValues[0], OnChangeTeam);

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

  FTeams := TTeams.Create;
end;

destructor TDefineTeamsFrm.Destroy;
begin
  if Assigned(FTeams) then
    FreeAndNil(FTeams);

  inherited;
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

procedure TDefineTeamsFrm.OnChangeTeam(Sender: TObject);
var
  Idx: Integer;
begin
  FTeams.SaveToFile(uTeams.cFileName);

  if not (Sender is TTeam) then
    Exit;

  Idx := FTeams.IndexOf(TTeam(Sender).Name);
  if Idx < 0 then
    Exit;

  TGenFunc.GetDefinedTeams(lbTeams, FTeams, OnChangeTeam, ListBoxItemClick, OnClickButton);

  lbTeams.ItemIndex := Idx;
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

function TDefineTeamsFrm.SetCaption: string;
begin
  Result := 'Teams Defined';
end;

function TDefineTeamsFrm.ShowBackButton: Boolean;
begin
  Result := True;
end;

function TDefineTeamsFrm.ShowOkButton: Boolean;
begin
  Result := False;
end;

end.
