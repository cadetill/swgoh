unit UBaseCheckFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.Edit,
  FMX.StdCtrls, FMX.ListBox, FMX.Layouts, FMX.Objects,
  FMX.Controls.Presentation, FMX.ScrollBox, FMX.Memo,
  UInterfaces, uUnit, uAbilities;

type
  TBaseCheckFrm = class(TForm, IChildren)
    mData: TMemo;
    lSteps: TLabel;
    Line2: TLine;
    lbID: TListBox;
    ListBoxItem1: TListBoxItem;
    Button1: TButton;
    pId: TPanel;
    lID: TLabel;
    eID: TEdit;
    bAdd: TButton;
    procedure bAddClick(Sender: TObject);
  private
  protected
    FChar: TUnitList;
    FShips: TUnitList;
    FAbi: TAbilities;

    procedure LoadUnitsFromFile;
    procedure OnClickButton(Sender: TObject);
  public
    function SetCaption: string; virtual;
    function ShowOkButton: Boolean; virtual;
    function ShowBackButton: Boolean; virtual;
    function AcceptForm: Boolean; virtual; abstract;
    procedure AfterShow; virtual;
  end;

var
  BaseCheckFrm: TBaseCheckFrm;

implementation

uses
  uGenFunc, uMessage, uCharacter, uShips;

{$R *.fmx}

procedure TBaseCheckFrm.AfterShow;
begin
  lSteps.Text := '';
  LoadUnitsFromFile;

  lbID.Clear;
  mData.Lines.Clear;
end;

procedure TBaseCheckFrm.bAddClick(Sender: TObject);
var
  lbItem: TListBoxItem;
  Button: TButton;
begin
  if eID.Text = '' then
    Exit;

  if Pos('http', eID.Text) <> 0 then
    eID.Text := TGenFunc.GetField(eID.Text, 5, '/');

  lbItem := TListBoxItem.Create(lbID);
  lbItem.Text := eID.Text;

  Button := TButton.Create(lbItem);
  Button.Align := TAlignLayout.Right;
  Button.Width := 40;
  Button.StyleLookup := 'trashtoolbutton';
  Button.Parent := lbItem;
  Button.OnClick := OnClickButton;

  lbID.AddObject(lbItem);

  eID.Text := '';
end;

procedure TBaseCheckFrm.LoadUnitsFromFile;
var
  L: TStringList;
begin
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

procedure TBaseCheckFrm.OnClickButton(Sender: TObject);
begin
  if not (Sender is TButton) then Exit;

  TMessage.MsjSiNo('Delete Selected ID: %s?', [TListBoxItem(TButton(Sender).Owner).Text],
    procedure
    begin
      lbID.RemoveObject(TListBoxItem(TButton(Sender).Owner));
    end);
end;

function TBaseCheckFrm.SetCaption: string;
begin
  Result := '';
end;

function TBaseCheckFrm.ShowBackButton: Boolean;
begin
  Result := True;
end;

function TBaseCheckFrm.ShowOkButton: Boolean;
begin
  Result := True;
end;

end.
