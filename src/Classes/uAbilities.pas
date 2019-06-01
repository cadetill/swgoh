unit uAbilities;

interface

uses
  uBase;

const
  cFileName = 'Abilities.json';

type
  TAbility = class(TBase)
  private
    FBase_id: string;
    FCharacter_base_id: string;
    FImage: string;
    FIs_omega: Boolean;
    FIs_zeta: Boolean;
    FName: string;
    FTier_max: Extended;
    FUrl: string;
  public
    class function FromJsonString(AJsonString: string): TAbility;

    property Base_id: string read FBase_id write FBase_id;
    property Character_base_id: string read FCharacter_base_id write FCharacter_base_id;
    property Image: string read FImage write FImage;
    property Is_omega: Boolean read FIs_omega write FIs_omega;
    property Is_zeta: Boolean read FIs_zeta write FIs_zeta;
    property Name: string read FName write FName;
    property Tier_max: Extended read FTier_max write FTier_max;
    property Url: string read FUrl write FUrl;
  end;

  TAbilities = class(TBase)
  private
    FItems: TArray<TAbility>;
    function GetCount: Integer;
  public
    destructor Destroy; override;

    procedure Compare(FileName: string); override;
    procedure AssignNoDefValues(Origin, Dest: TAbility);
    function IndexOf(BaseId: string): Integer;
    function NextAbility(CharacterBaseId: string; ActualPos: Integer = 0): Integer;

    class function FromJsonString(AJsonString: string): TAbilities;

    property Items: TArray<TAbility> read FItems write FItems;
    property Count: Integer read GetCount;
  end;

implementation

uses
  Rest.Json, System.Classes, System.SysUtils, System.IOUtils;

{ TAbility }

class function TAbility.FromJsonString(AJsonString: string): TAbility;
begin
  Result := TJson.JsonToObject<TAbility>(AJsonString);
end;

{ TAbilities }

procedure TAbilities.AssignNoDefValues(Origin, Dest: TAbility);
begin

end;

procedure TAbilities.Compare(FileName: string);
var
  List: TAbilities;
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
    List := TAbilities.FromJsonString(L.Text);
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

destructor TAbilities.Destroy;
var
  LItemsItem: TAbility;
begin
 for LItemsItem in FItems do
   LItemsItem.Free;

  inherited;
end;

class function TAbilities.FromJsonString(AJsonString: string): TAbilities;
begin
  Result := TJson.JsonToObject<TAbilities>(AJsonString);
end;

function TAbilities.GetCount: Integer;
begin
  Result := High(FItems);
end;

function TAbilities.IndexOf(BaseId: string): Integer;
var
  i: Integer;
begin
  Result := -1;
  for i := 0 to Count do
    if SameText(FItems[i].Base_Id, BaseId) then
    begin
      Result := i;
      Break;
    end;
end;

function TAbilities.NextAbility(CharacterBaseId: string;
  ActualPos: Integer): Integer;
var
  i: Integer;
begin
  Result := -1;
  for i := ActualPos+1 to Count do
    if SameText(FItems[i].Character_base_id, CharacterBaseId) then
    begin
      Result := i;
      Break;
    end;
end;

end.
