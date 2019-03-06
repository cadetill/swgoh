unit UDefineTeamsFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.StdCtrls,
  FMX.Controls.Presentation, FMX.Edit, FMX.SearchBox, FMX.Layouts, FMX.ListBox,
  uTeams, uInterfaces;

type
  TDefineTeamsFrm = class(TForm)
    lbTeams: TListBox;
    SearchBox1: TSearchBox;
    bAdd: TButton;
    ListBoxItem1: TListBoxItem;
    pChkGuild: TPanel;
    lChkGuild: TLabel;
    eChkGuild: TEdit;
    bChkGuild: TButton;
    procedure bAddClick(Sender: TObject);
    procedure bChkGuildClick(Sender: TObject);
  private
    FTeams: TTeams;

    procedure OnChangeTeam(Sender: TObject);

    procedure GetDefinedTeams;
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
  uBase, uMessage, UTeamFrm, uGenFunc, UTeamCheck,
  FMX.DialogService, System.IOUtils;

{$R *.fmx}

{ TDefineTeamsFrm }

function TDefineTeamsFrm.AcceptForm: Boolean;
begin
  Result := True;
end;

procedure TDefineTeamsFrm.AfterShow;
begin

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

procedure TDefineTeamsFrm.bChkGuildClick(Sender: TObject);
var
  Intf: IMainMenu;
begin
  if eChkGuild.Text = '' then
    Exit;

  if Pos('http', eChkGuild.Text) <> 0 then
    eChkGuild.Text := TGenFunc.GetField(eChkGuild.Text, 5, '/');

  if Assigned(TagObject) then
    TagObject.Free;

  TagObject := TFmxObject.Create(Self);
  TFmxObject(TagObject).TagString := eChkGuild.Text;

  // si es pot, creem formulari d'assistència
  if Supports(Owner, IMainMenu, Intf) then
    Intf.CreateForm(TTeamCheck, TagObject);
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
  Fixed: string;
  NoFix: string;
begin
  lbTeams.Clear;

  if Assigned(FTeams) then
    FreeAndNil(FTeams);

  if not TFile.Exists(uTeams.cFileName) then
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
    FTeams.Items[i].OnChange := OnChangeTeam;

    lbItem := TListBoxItem.Create(lbTeams);
    lbItem.Text := FTeams.Items[i].Name;

    lbItem.ItemData.Detail := '';
    Fixed := '';
    NoFix := '';
    for j := 0 to FTeams.Items[i].Count do
    begin
      if FTeams.Items[i].Units[j].Fixed then
      begin
        if Fixed <> '' then Fixed := Fixed + ' / ';
        if FTeams.Items[i].Units[j].Alias = '' then
          Fixed := Fixed + FTeams.Items[i].Units[j].Name
        else
          Fixed := Fixed + FTeams.Items[i].Units[j].Alias;
      end
      else
      begin
        if NoFix <> '' then NoFix := NoFix + ' / ';
        if FTeams.Items[i].Units[j].Alias = '' then
          NoFix := NoFix + '*' + FTeams.Items[i].Units[j].Name
        else
          NoFix := NoFix + '*' + FTeams.Items[i].Units[j].Alias;
      end;
    end;
    lbItem.ItemData.Detail := Fixed;
    if (lbItem.ItemData.Detail <> '') and (NoFix <> '') then
      lbItem.ItemData.Detail := lbItem.ItemData.Detail + ' / ';
    lbItem.ItemData.Detail := lbItem.ItemData.Detail + NoFix;

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

procedure TDefineTeamsFrm.OnChangeTeam(Sender: TObject);
var
  Idx: Integer;
  i: Integer;
  Fixed: string;
  NoFix: string;
begin
  FTeams.SaveToFile(uTeams.cFileName);

  if not (Sender is TTeam) then
    Exit;

  Idx := FTeams.IndexOf(TTeam(Sender).Name);
  if Idx < 0 then
    Exit;

  lbTeams.ListItems[Idx].ItemData.Text := TTeam(Sender).Name;
  lbTeams.ListItems[Idx].ItemData.Detail := '';
  Fixed := '';
  NoFix := '';
  for i := 0 to TTeam(Sender).Count do
  begin
    if TTeam(Sender).Units[i].Fixed then
    begin
      if Fixed <> '' then Fixed := Fixed + ' / ';
      if TTeam(Sender).Units[i].Alias = '' then
        Fixed := Fixed + TTeam(Sender).Units[i].Name
      else
        Fixed := Fixed + TTeam(Sender).Units[i].Alias;
    end
    else
    begin
      if NoFix <> '' then NoFix := NoFix + ' / ';
      if TTeam(Sender).Units[i].Alias = '' then
        NoFix := NoFix + '*' + TTeam(Sender).Units[i].Name
      else
        NoFix := NoFix + '*' + TTeam(Sender).Units[i].Alias;
    end;
  end;
  lbTeams.ListItems[Idx].ItemData.Detail := Fixed;
  if (lbTeams.ListItems[Idx].ItemData.Detail <> '') and (NoFix <> '') then
    lbTeams.ListItems[Idx].ItemData.Detail := lbTeams.ListItems[Idx].ItemData.Detail + ' / ';
  lbTeams.ListItems[Idx].ItemData.Detail := lbTeams.ListItems[Idx].ItemData.Detail + NoFix;
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
