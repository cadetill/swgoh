unit uCharacter;

interface

uses
  uUnit;

const
  cFileName = 'Characters.json';

type
  TCharacters = class(TUnitList)
  private
  public
    procedure Compare(FileName: string); override;

    class function FromJsonString(AJsonString: string): TUnitList; override;
  end;

implementation

uses
  Rest.Json, System.Classes, System.SysUtils, System.IOUtils;

{ TCharacters }

procedure TCharacters.Compare(FileName: string);
var
  OldList: TUnitList;
  L: TStringList;
  i: Integer;
  Idx: Integer;
begin
  inherited;

  // si el fitxer no existeix, sortim
  if not TFile.Exists(FileName) then
    Exit;

  // carreguem fitxer existent
  L := TStringList.Create;
  try
    L.LoadFromFile(FileName);
    OldList := TCharacters.FromJsonString(L.Text);
  finally
    FreeAndNil(L);
  end;

  // recorrem fitxer existent actualitzant camps propis al nou
  for i := 0 to Count do
  begin
    Idx := OldList.IndexOf(Items[i].Base_Id);
    if Idx <> -1 then // si el trobem
      Self.AssignNoDefValues(OldList.Items[Idx], Self.Items[i]);
  end;
end;

class function TCharacters.FromJsonString(AJsonString: string): TUnitList;
begin
  Result := TJson.JsonToObject<TCharacters>(AJsonString);
end;

end.
