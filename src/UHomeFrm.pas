unit UHomeFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs,
  FMX.Controls.Presentation, FMX.Edit, FMX.SearchBox, FMX.Layouts, FMX.ListBox,
  uUnit;

type
  THomeFrm = class(TForm)
    lbUnits: TListBox;
    SearchBox1: TSearchBox;
    ListBoxGroupHeader1: TListBoxGroupHeader;
    ListBoxItem1: TListBoxItem;
    ListBoxItem2: TListBoxItem;
  private
    FChar: TUnitList;
    FShips: TUnitList;

    procedure ListBoxItemClick(Sender: TObject);
  public
    constructor Create(AOwner: TComponent); override;

    procedure LoadDataFromFile;
  end;

var
  HomeFrm: THomeFrm;

implementation

uses
  FMX.DialogService, System.IOUtils,
  uCharacter, uShips;

{$R *.fmx}

{ THomeFrm }

constructor THomeFrm.Create(AOwner: TComponent);
begin
  inherited;

  LoadDataFromFile;
end;

procedure THomeFrm.ListBoxItemClick(Sender: TObject);
var
  Pos: Integer;
  List: TUnitList;
  FileName: string;
  TmpS: string;
begin
  if not (Sender is TListBoxItem) then
    Exit;

  List := nil;
  FileName := '';
  Pos := FChar.IndexOf(TListBoxItem(Sender).TagString);
  if Pos <> -1 then
  begin
    List := FChar;
    FileName := uCharacter.cFileName;
  end
  else
  begin
    Pos := FShips.IndexOf(TListBoxItem(Sender).TagString);
    if Pos <> -1 then
    begin
      List := FShips;
      FileName := uShips.cFileName;
    end
  end;

  if not Assigned(List) then
    Exit;

  if List.Items[Pos].Alias = '' then
    TmpS := List.Items[Pos].Name
  else
    TmpS := List.Items[Pos].Alias;
  TDialogService.InputQuery('Set Multiplier', ['Multiplier', 'Alias'], [List.Items[Pos].Multiplier.ToString, TmpS],
    procedure(const AResult: TModalResult; const AValues: array of string)
    var
      TmpInt: Integer;
    begin
      if AResult = mrOk then
      begin
        if TryStrToInt(AValues[0], TmpInt) then
          List.Items[Pos].Multiplier := TmpInt;
        if SameText(List.Items[Pos].Name, AValues[1]) then
          List.Items[Pos].Alias := ''
        else
          List.Items[Pos].Alias := AValues[1];
        List.SaveToFile(FileName);
        TListBoxItem(Sender).ItemData.Detail := 'Alias: ' + List.Items[Pos].Alias + ' / Multiplier: ' + List.Items[Pos].Multiplier.ToString;
      end;
    end);
end;

procedure THomeFrm.LoadDataFromFile;
var
  L: TStringList;
  i: Integer;
  lbGH: TListBoxGroupHeader;
  lbItem: TListBoxItem;
begin
  lbUnits.Clear;

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

    // afegim capçalera
    lbGH := TListBoxGroupHeader.Create(lbUnits);
    lbGH.TextSettings.HorzAlign := TTextAlign.Center;
    lbGH.TextSettings.FontColor := TAlphaColorRec.Chocolate;
    lbGH.Text := 'Characters';
    lbUnits.AddObject(lbGH);

    for i := 0 to FChar.Count do
    begin
      lbItem := TListBoxItem.Create(lbUnits);
      lbItem.Text := FChar.Items[i].Name;
      lbItem.TagString := FChar.Items[i].Base_Id;
      lbItem.ItemData.Detail := 'Alias: ' + FChar.Items[i].Alias + ' / Multiplier: ' + FChar.Items[i].Multiplier.ToString;
      lbItem.ItemData.Accessory := TListBoxItemData.TAccessory.aDetail;
      lbItem.OnClick := ListBoxItemClick;
      lbUnits.AddObject(lbItem);
    end;
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

    // afegim capçalera
    lbGH := TListBoxGroupHeader.Create(lbUnits);
    lbGH.TextSettings.HorzAlign := TTextAlign.Center;
    lbGH.TextSettings.FontColor := TAlphaColorRec.Chocolate;
    lbGH.Text := 'Ships';
    lbUnits.AddObject(lbGH);

    for i := 0 to FShips.Count do
    begin
      lbItem := TListBoxItem.Create(lbUnits);
      lbItem.Text := FShips.Items[i].Name;
      lbItem.TagString := FShips.Items[i].Base_Id;
      lbItem.ItemData.Detail := 'Alias: ' + FShips.Items[i].Alias + ' / Multiplier: ' + FShips.Items[i].Multiplier.ToString;
      lbItem.ItemData.Accessory := TListBoxItemData.TAccessory.aDetail;
      lbItem.OnClick := ListBoxItemClick;
      lbUnits.AddObject(lbItem);
    end;
  end;
end;

end.
