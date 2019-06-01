unit UMainFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs,
  IdBaseComponent, IdComponent, IdTCPConnection, IdTCPClient, IdHTTP,
  FMX.Controls.Presentation, FMX.StdCtrls, FMX.ScrollBox, FMX.Memo,
  IPPeerClient, REST.Client, Data.Bind.Components, Data.Bind.ObjectScope,
  FireDAC.Stan.Intf, FireDAC.Stan.Option, FireDAC.Stan.Param,
  FireDAC.Stan.Error, FireDAC.DatS, FireDAC.Phys.Intf, FireDAC.DApt.Intf,
  Data.DB, FireDAC.Comp.DataSet, FireDAC.Comp.Client, REST.Response.Adapter,
  REST.Types, FMX.Menus, FMX.Layouts, FMX.ListBox, System.Generics.Collections,
  FMX.MultiView,
  uInterfaces;

type
  TUnitType = (utChar, utShip);

  TMainFrm = class(TForm, IMainMenu)
    mvMenu: TMultiView;
    bGetDataWeb: TButton;
    pContent: TPanel;
    tbHeader: TToolBar;
    bBack: TButton;
    bMultiView: TButton;
    aiIndicator: TAniIndicator;
    bSetToSum: TButton;
    bDefineTeams: TButton;
    bCheckPlayer: TButton;
    bComparePlayers: TButton;
    bCheckGuilds: TButton;
    lHeader: TLabel;
    bOk: TButton;
    bCheckTeams: TButton;
    lbMenu: TListBox;
    lbiGetDataWeb: TListBoxItem;
    lbiSetToSum: TListBoxItem;
    lbiDefineTeams: TListBoxItem;
    lbiComparePlayers: TListBoxItem;
    lbiCheckPlayer: TListBoxItem;
    lbiCheckGuilds: TListBoxItem;
    lbiCheckTeams: TListBoxItem;
    lbiGetInfo: TListBoxGroupHeader;
    lbiConfig: TListBoxGroupHeader;
    lbiChecks: TListBoxGroupHeader;
    lbiDefineGear: TListBoxItem;
    bDefineGear: TButton;
    lbiCheckGear: TListBoxItem;
    bCheckGear: TButton;
    procedure bBackClick(Sender: TObject);
    procedure bGetDataWebClick(Sender: TObject);
    procedure bSetToSumClick(Sender: TObject);
    procedure bDefineTeamsClick(Sender: TObject);
    procedure bCheckPlayerClick(Sender: TObject);
    procedure bOkClick(Sender: TObject);
    procedure bCheckGuildsClick(Sender: TObject);
    procedure bComparePlayersClick(Sender: TObject);
    procedure bCheckTeamsClick(Sender: TObject);
    procedure bDefineGearClick(Sender: TObject);
    procedure bCheckGearClick(Sender: TObject);
  private
    FFrmList: TObjectList<TCustomForm>;

    procedure PushForm(AForm: TCustomForm);
    procedure PopForm;

    procedure CreateForm(ClassForm: TFmxObjectClass; DataObject: TObject);
    procedure ShowAcceptButton(State: Boolean);
    function ShowAni(Show: Boolean): Boolean;
  public
    constructor Create(AOwner: TComponent); override;
    destructor Destroy; override;
  end;

var
  MainFrm: TMainFrm;

implementation

uses
  uRESTMdl, UHomeFrm, UToSumFrm, UDefineTeamsFrm, UCheckPlayerFrm, UCheckGuildsFrm,
  UCheckTeamsFrm, UCompPlayersFrm, UDefineGearFrm, UCheckGearFrm,
  System.JSON.Serializers;

{$R *.fmx}

procedure TMainFrm.bBackClick(Sender: TObject);
begin
  if FFrmList.Count > 1 then
    PopForm
  else
    Close;
end;

procedure TMainFrm.bCheckGearClick(Sender: TObject);
begin
  CreateForm(TCheckGearFrm, nil);
end;

procedure TMainFrm.bCheckGuildsClick(Sender: TObject);
begin
  CreateForm(TCheckGuildsFrm, nil);
end;

procedure TMainFrm.bCheckPlayerClick(Sender: TObject);
begin
  CreateForm(TCheckPlayerFrm, nil);
end;

procedure TMainFrm.bCheckTeamsClick(Sender: TObject);
begin
  CreateForm(TCheckTeamsFrm, nil);
end;

procedure TMainFrm.bComparePlayersClick(Sender: TObject);
begin
  CreateForm(TCompPlayersFrm, nil);
end;

procedure TMainFrm.bDefineGearClick(Sender: TObject);
begin
  CreateForm(TDefineGearFrm, nil);
end;

procedure TMainFrm.bDefineTeamsClick(Sender: TObject);
begin
  CreateForm(TDefineTeamsFrm, nil);
end;

procedure TMainFrm.bGetDataWebClick(Sender: TObject);
begin
  mvMenu.HideMaster;
  ShowAni(True);

  TThread.CreateAnonymousThread(procedure
  var
    Mdl: TRESTMdl;
    i: Integer;
  begin
    // realitzem procés
    Mdl := TRESTMdl.Create(nil);
    try
      Mdl.LoadData(tcUnits, '');
    finally
      FreeAndNil(Mdl);
    end;

    // si cal, refresquem TListBox
    for i := 0 to FFrmList.Count - 1 do
      if FFrmList[i] is THomeFrm then
        THomeFrm(FFrmList[i]).LoadDataFromFile;

    // ocultem animació
    TThread.Synchronize(TThread.CurrentThread,
      procedure
      begin
        ShowAni(False);
      end);
  end
  ).Start;
end;

procedure TMainFrm.bOkClick(Sender: TObject);
var
  Intf: IChildren;
  AForm: TCustomForm;
begin
  if FFrmList.Count = 0 then
    Exit;

  AForm := FFrmList[FFrmList.Count - 1];

  if Supports(AForm, IChildren, Intf) and Intf.AcceptForm then
    PopForm;
end;

procedure TMainFrm.bSetToSumClick(Sender: TObject);
begin
  CreateForm(TToSumFrm, nil);
end;

constructor TMainFrm.Create(AOwner: TComponent);
begin
  inherited;

  FFrmList := TObjectList<TCustomForm>.Create;
  ShowAni(False);
  lHeader.Text := '';

  CreateForm(THomeFrm, nil);
end;

procedure TMainFrm.CreateForm(ClassForm: TFmxObjectClass; DataObject: TObject);
var
  aForm: TCustomForm;
begin
  inherited;

  mvMenu.HideMaster;
  aForm := ClassForm.Create(Self) as TCustomForm;
  aForm.TagObject := DataObject;
  aForm.Name := aForm.Name + FormatDateTime('hhnnssmm', Now);
  PushForm(aForm);
end;

destructor TMainFrm.Destroy;
begin
  if Assigned(FFrmList) then
    FreeAndNil(FFrmList);

  inherited;
end;

procedure TMainFrm.PopForm;
var
  AForm: TCustomForm;
  Intf: IChildren;
begin
  // if don't have stack forms, bye bye
  if FFrmList.Count = 0 then
    Exit;

  // we return parent references
  while pContent.ChildrenCount > 0 do
    pContent.Children[0].Parent := FFrmList.Items[FFrmList.Count - 1];

  // unstack last shown form
  FFrmList.Delete(FFrmList.Count - 1);

  lHeader.Text := '';
  bOk.Visible := False;

  // if any form is into the stack
  if FFrmList.Count > 0 then
  begin
    // get last form
    AForm := FFrmList.Items[FFrmList.Count - 1];

    // put new references to the principal container
    while AForm.ChildrenCount > 0 do
      AForm.Children[0].Parent := pContent;

    if Supports(AForm, IChildren, Intf) then
    begin
      lHeader.Text := Intf.SetCaption;
      bBack.Visible := Intf.ShowBackButton;
      bOk.Visible := Intf.ShowOkButton;

      Intf.AfterShow;
    end;
  end;
end;

procedure TMainFrm.PushForm(AForm: TCustomForm);
var
  Intf: IChildren;
begin
  if FFrmList.Count > 0 then
  begin
    while pContent.ChildrenCount > 0 do
      pContent.Children[0].Parent := FFrmList.Items[FFrmList.Count - 1];
  end;

  FFrmList.Add(AForm);
  bOk.Visible := False;

  while AForm.ChildrenCount > 0 do
    AForm.Children[0].Parent := pContent;

  if Supports(AForm, IChildren, Intf) then
  begin
    lHeader.Text := Intf.SetCaption;
    bBack.Visible := Intf.ShowBackButton;
    bOk.Visible := Intf.ShowOkButton;

    Intf.AfterShow;
  end;
end;

procedure TMainFrm.ShowAcceptButton(State: Boolean);
begin
//  bAccept.Visible := State;
end;

function TMainFrm.ShowAni(Show: Boolean): Boolean;
begin
  Result := aiIndicator.Enabled;
  aiIndicator.Enabled := Show;
  aiIndicator.Visible := Show;
end;

end.
