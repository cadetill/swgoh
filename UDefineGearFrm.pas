unit UDefineGearFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.Edit,
  FMX.SearchBox, FMX.Controls.Presentation, FMX.StdCtrls, FMX.ListBox,
  FMX.Layouts, FMX.Objects,
  uInterfaces, uGear;


type
  TDefineGearFrm = class(TForm, IChildren)
    lbGears: TListBox;
    ListBoxItem1: TListBoxItem;
    ListBoxItem2: TListBoxItem;
    SearchBox1: TSearchBox;
    bToClbd: TButton;
    Panel1: TPanel;
    cbShowImages: TCheckBox;
    procedure cbShowImagesClick(Sender: TObject);
  private
    FGear: TGear;

    procedure ListBoxItemClick(Sender: TObject);
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
  DefineGearFrm: TDefineGearFrm;

implementation

uses
  uGenFunc,
  FMX.DialogService, System.IOUtils;

{$R *.fmx}

{ TDefineGearFrm }

function TDefineGearFrm.AcceptForm: Boolean;
begin
  Result := True;
end;

procedure TDefineGearFrm.AfterShow;
var
  L: TStringList;
  i: Integer;
  lbItem: TListBoxItem;
  Img: TImage;
begin
  lbGears.Clear;

  // carreguem gears
  if TFile.Exists(TGenFunc.GetBaseFolder + uGear.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(TGenFunc.GetBaseFolder + uGear.cFileName);
      FGear := TGear.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;

    for i := 0 to FGear.Count do
    begin
      lbItem := TListBoxItem.Create(lbGears);
      lbItem.Text := FGear.Items[i].Name;
      lbItem.TagString := FGear.Items[i].Base_Id;
      lbItem.ItemData.Detail := 'Alias: ' + FGear.Items[i].Alias + ' / To Check: ' + BoolToStr(FGear.Items[i].ToCheck, True);
      lbItem.ItemData.Accessory := TListBoxItemData.TAccessory.aDetail;
      lbItem.Height := 25;
      lbItem.OnClick := ListBoxItemClick;
      lbGears.AddObject(lbItem);

      Img := TImage.Create(lbItem);
      Img.Parent := lbItem;
      Img.Align := TAlignLayout.Left;
      Img.Width := lbItem.Height;
      Img.Name := 'img' + lbItem.Index.ToString;
    end;
  end;
end;

procedure TDefineGearFrm.cbShowImagesClick(Sender: TObject);
var
  i: Integer;
  Comp: TComponent;
  Idx: Integer;
begin
  for i := 0 to lbGears.Count - 1 do
  begin
    Comp := lbGears.ItemByIndex(i).FindComponent('img' + lbGears.ItemByIndex(i).Index.ToString);
    if Assigned(Comp) then
    begin
      if not TFile.Exists(TGenFunc.GetImgFolder + lbGears.ItemByIndex(i).TagString) then
      begin
        Idx := FGear.IndexOf(lbGears.ItemByIndex(i).TagString);
        if Idx = -1 then
          Continue;

        TGenFunc.DownloadImgFromWeb('https:' + FGear.Items[Idx].Image, TGenFunc.GetImgFolder + lbGears.ItemByIndex(i).TagString);
      end;

      TImage(Comp).Bitmap.LoadFromFile(TGenFunc.GetImgFolder + lbGears.ItemByIndex(i).TagString)
    end;
  end;
end;

constructor TDefineGearFrm.Create(AOwner: TComponent);
begin
  inherited;

  FGear := TGear.Create;
end;

destructor TDefineGearFrm.Destroy;
begin
  if Assigned(FGear) then
    FreeAndNil(FGear);

  inherited;
end;

procedure TDefineGearFrm.ListBoxItemClick(Sender: TObject);
var
  Pos: Integer;
  FileName: string;
  TmpS: string;
begin
  if not (Sender is TListBoxItem) then
    Exit;

  FileName := '';
  Pos := FGear.IndexOf(TListBoxItem(Sender).TagString);
  if Pos = -1 then
    Exit;

  FileName := TGenFunc.GetBaseFolder + uGear.cFileName;

  if FGear.Items[Pos].Alias = '' then
    TmpS := FGear.Items[Pos].Name
  else
    TmpS := FGear.Items[Pos].Alias;

  TDialogService.InputQuery('Configuration', ['ToCheck (0=False)', 'Alias'], [FGear.Items[Pos].ToCheck.ToString, TmpS],
    procedure(const AResult: TModalResult; const AValues: array of string)
    var
      TmpBol: Boolean;
    begin
      if AResult = mrOk then
      begin
        if TryStrToBool(AValues[0], TmpBol) then
          FGear.Items[Pos].ToCheck := TmpBol;
        if SameText(FGear.Items[Pos].Name, AValues[1]) then
          FGear.Items[Pos].Alias := ''
        else
          FGear.Items[Pos].Alias := AValues[1];
        FGear.SaveToFile(FileName);
        TListBoxItem(Sender).ItemData.Detail := 'Alias: ' + FGear.Items[Pos].Alias + ' / To Check: ' + BoolToStr(FGear.Items[Pos].ToCheck, True);
      end;
    end);
end;

function TDefineGearFrm.SetCaption: string;
begin
  Result := 'Defined Gears';
end;

function TDefineGearFrm.ShowBackButton: Boolean;
begin
  Result := True;
end;

function TDefineGearFrm.ShowOkButton: Boolean;
begin
  Result := False;
end;

end.
