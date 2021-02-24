unit UCheckGearFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.StdCtrls,
  FMX.Controls.Presentation, FMX.ScrollBox, FMX.Memo, FMX.Layouts, FMX.ListBox,
  FMX.Edit, FMX.SearchBox,
  uInterfaces, uUnit, uGear, FireDAC.Stan.Intf, FireDAC.Stan.Option,
  FireDAC.Stan.Param, FireDAC.Stan.Error, FireDAC.DatS, FireDAC.Phys.Intf,
  FireDAC.DApt.Intf, Data.DB, FireDAC.Comp.DataSet, FireDAC.Comp.Client;

type
  TCheckGearFrm = class(TForm, IChildren)
    lbUnits: TListBox;
    mData: TMemo;
    bToClbd: TButton;
    SearchBox1: TSearchBox;
    Label1: TLabel;
    tData: TFDMemTable;
    procedure bToClbdClick(Sender: TObject);
  private
    FChar: TUnitList;
    FGear: TGear;
  public
    function SetCaption: string;
    function ShowOkButton: Boolean;
    function ShowBackButton: Boolean;
    function AcceptForm: Boolean;
    procedure AfterShow;
  end;

var
  CheckGearFrm: TCheckGearFrm;

implementation

uses
  System.IOUtils,
  uGenFunc, uCharacter, uIniFiles;

{$R *.fmx}

{ TCheckGearFrm }

function TCheckGearFrm.AcceptForm: Boolean;
  procedure AddRecord(FieldName, BaseId, Alias, Name: string; Quantity: Integer);
  begin
    if tData.FindKey([BaseId]) then
      tData.Edit
    else
    begin
      tData.Append;
      tData.FieldByName('BaseId').AsString := BaseId;
      tData.FieldByName('Alias').AsString := Alias;
      if Alias = '' then
        tData.FieldByName('Alias').AsString := Name;
    end;
    tData.FieldByName(FieldName).AsInteger := tData.FieldByName(FieldName).AsInteger + Quantity;
    tData.Post;
  end;
var
  Idx: Integer;
  IdxG: Integer;
  TmpI: Integer;
  TmpS: string;
  i,j,k: Integer;
  MaxGear: Integer;
begin
  Result := False;
  mData.Lines.Clear;

  if lbUnits.ItemIndex = -1 then
    Exit;

  Idx := FChar.IndexOf(lbUnits.ItemByIndex(lbUnits.ItemIndex).TagString);
  if Idx = -1 then
    Exit;

  TFileIni.SetFileIni(TGenFunc.GetIniName);
  MaxGear := TFileIni.GetIntValue('GEAR', 'MAXGEAR', 0);

  if tData.Active then
    tData.Close;
  tData.FieldDefs.Clear;
  tData.IndexDefs.Clear;
  with tData.FieldDefs.AddFieldDef do
  begin
    DataType := ftString;
    Size := 100;
    Name := 'BaseId';
  end;
  with tData.FieldDefs.AddFieldDef do
  begin
    DataType := ftString;
    Size := 100;
    Name := 'Alias';
  end;
  for i := MaxGear downto 1 do
    with tData.FieldDefs.AddFieldDef do
    begin
      DataType := ftInteger;
      Name := 'L' + i.ToString;
    end;
  with tData.IndexDefs.AddIndexDef do
  begin
    Fields := 'BaseId';
    Name := 'Primary';
  end;
  tData.CreateDataSet;
  tData.IndexName := 'Primary';

  for i := FChar.Items[Idx].CountGL downto 0 do
  begin
    for j := 0 to FChar.Items[Idx].Gear_levels[i].Count do
    begin
      IdxG := FGear.IndexOf(FChar.Items[Idx].Gear_levels[i].Gear[j]);

      if FGear.Items[IdxG].ToCheck then
      begin
        AddRecord('L' + (i+1).ToString, FGear.Items[IdxG].Base_id, FGear.Items[IdxG].Alias, FGear.Items[IdxG].Name, 1);
      end
      else
        if FGear.Items[IdxG].Count > -1 then
        begin
          for k := 0 to FGear.Items[IdxG].Count do
          begin
            TmpI := FGear.IndexOf(FGear.Items[IdxG].Ingredients[k].Gear);
            if TmpI < 0 then
              Continue;
            if FGear.Items[TmpI].ToCheck then
              AddRecord('L' + (i+1).ToString, FGear.Items[TmpI].Base_id, FGear.Items[TmpI].Alias, FGear.Items[TmpI].Name, Trunc(FGear.Items[IdxG].Ingredients[k].Amount));
          end;
        end;
    end;
  end;

  TmpS := FChar.Items[Idx].Name + #9;
  for i := 2 to tData.FieldDefs.Count - 1 do
    TmpS := TmpS + #9 + tData.Fields[i].FieldName.Substring(1);
  mData.Lines.Add(TmpS);

  tData.First;
  while not tData.Eof do
  begin
    TmpS := tData.FieldByName('Alias').AsString + #9;
    for i := 2 to tData.FieldDefs.Count - 1 do
      TmpS := TmpS + #9 + tData.Fields[i].AsString;

    mData.Lines.Add(TmpS);

    tData.Next;
  end;
end;

procedure TCheckGearFrm.AfterShow;
var
  L: TStringList;
  i: Integer;
  lbItem: TListBoxItem;
begin
  lbUnits.Clear;

  // carreguem personatges
  if TFile.Exists(TGenFunc.GetBaseFolder + uCharacter.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(TGenFunc.GetBaseFolder + uCharacter.cFileName);
      FChar := TCharacters.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;

    for i := 0 to FChar.Count do
    begin
      lbItem := TListBoxItem.Create(lbUnits);
      lbItem.Text := FChar.Items[i].Name;
      lbItem.TagString := FChar.Items[i].Base_Id;
      lbUnits.AddObject(lbItem);
    end;
  end;

  // carreguem Gears
  if TFile.Exists(TGenFunc.GetBaseFolder + uGear.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(TGenFunc.GetBaseFolder + uGear.cFileName);
      FGear := TGear.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;
  end;
end;

procedure TCheckGearFrm.bToClbdClick(Sender: TObject);
begin
  TGenFunc.CopyToClipboard(mData.Lines.Text)
end;

function TCheckGearFrm.SetCaption: string;
begin
  Result := '';
end;

function TCheckGearFrm.ShowBackButton: Boolean;
begin
  Result := True;
end;

function TCheckGearFrm.ShowOkButton: Boolean;
begin
  Result := True;
end;

end.
