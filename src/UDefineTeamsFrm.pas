unit UDefineTeamsFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.StdCtrls,
  FMX.Controls.Presentation, FMX.Edit, FMX.SearchBox, FMX.Layouts, FMX.ListBox,
  uTeams, uInterfaces, uAbilities;

type
  TDefineTeamsFrm = class(TForm, IChildren)
    lbTeams: TListBox;
    SearchBox1: TSearchBox;
    bAdd: TButton;
    ListBoxItem1: TListBoxItem;
    bToClbd: TButton;
    procedure bAddClick(Sender: TObject);
    procedure bToClbdClick(Sender: TObject);
  private
    FTeams: TTeams;
    FAbi: TAbilities;

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
  FMX.DialogService,
  System.IOUtils;

{$R *.fmx}

{ TDefineTeamsFrm }

function TDefineTeamsFrm.AcceptForm: Boolean;
begin
  Result := True;
end;

procedure TDefineTeamsFrm.AfterShow;
var
  L: TStringList;
begin
  TGenFunc.GetDefinedTeams(lbTeams, FTeams, OnChangeTeam, ListBoxItemClick, OnClickButton);

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

procedure TDefineTeamsFrm.bToClbdClick(Sender: TObject);
var
  i,j,k: Integer;
  L: TStringList;
  TmpStr: string;
  NameAb: string;
  Idx: Integer;
begin
  L := TStringList.Create;
  try
    for i := 0 to FTeams.Count do
    begin
      L.Add('Team: ' + FTeams.Items[i].Name);
      for j := 0 to FTeams.Items[i].Count do
      begin
        TmpStr := '';
        for k := 0 to FTeams.Items[i].Units[j].Count do
        begin
          if TmpStr <> '' then
            TmpStr := TmpStr + ' ; '
          else
            TmpStr := ' -> zetas needed: ';

          Idx := FAbi.IndexOf(FTeams.Items[i].Units[j].Zetas[k]);
          if Idx <> -1 then
            NameAb := FAbi.Items[Idx].Name
          else
            NameAb := FTeams.Items[i].Units[j].Zetas[k];

          TmpStr := TmpStr + NameAb;
        end;

        if FTeams.Items[i].Units[j].Fixed then
          L.Add('  - ' + FTeams.Items[i].Units[j].Name + TmpStr)
        else
          L.Add('  - (*)' + FTeams.Items[i].Units[j].Name + TmpStr);
      end;
    end;

    TGenFunc.CopyToClipboard(L.Text);
  finally
    FreeAndNil(L);
  end;
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
