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
  Rest.Json, System.Classes, System.SysUtils;

{ TCharacters }

procedure TCharacters.Compare(FileName: string);
var
  List: TUnitList;
  L: TStringList;
  i: Integer;
  Idx: Integer;
begin
  inherited;

  // si el fitxer no existeix, sortim
  if not FileExists(FileName) then
    Exit;

  // carreguem fitxer existent
  L := TStringList.Create;
  try
    L.LoadFromFile(FileName);
    List := TCharacters.FromJsonString(L.Text);
  finally
    FreeAndNil(L);
  end;

  // recorrem fitxer existent actualitzant camps propis al nou
  for i := 0 to List.Count do
  begin
    Idx := Self.IndexOf(List.Items[i].Base_Id);
    if Idx > 0 then // si el trobem
      Self.AssignNoDefValues(List.Items[i], Self.Items[i]);
  end;
end;

class function TCharacters.FromJsonString(AJsonString: string): TUnitList;
begin
  Result := TJson.JsonToObject<TCharacters>(AJsonString);
end;

end.
