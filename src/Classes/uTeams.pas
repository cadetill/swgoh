unit uTeams;

interface

uses
  uBase;

const
  cFileName = 'Teams.json';

type
  TUnitTeam = class(TBase)
  private
    FBase_id: string;
    FFixed: string;
    FGear: string;
    FSpeed: string;
    FStars: string;
    FZetas: TArray<string>;
    FName: string;
  public
    class function FromJsonString(AJsonString: string): TUnitTeam;

    property Base_id: string read FBase_id write FBase_id;
    property Name: string read FName write FName;
    property Fixed: string read FFixed write FFixed;
    property Gear: string read FGear write FGear;
    property Speed: string read FSpeed write FSpeed;
    property Stars: string read FStars write FStars;
    property Zetas: TArray<String> read FZetas write FZetas;
  end;

  TTeam = class(TBase)
  private
    FName: string;
    FUnits: TArray<TUnitTeam>;
    function GetCount: Integer;
  public
    destructor Destroy; override;

    function IndexOf(Name: string): Integer;
    function AddUnit(BaseId, Name: string): TUnitTeam;
    procedure DeleteUnit(Name: string);

    class function FromJsonString(AJsonString: string): TTeam;

    property Name: string read FName write FName;
    property Units: TArray<TUnitTeam> read FUnits write FUnits;
    property Count: Integer read GetCount;
  end;

  TTeams = class(TBase)
  private
    FItems: TArray<TTeam>;
    function GetCount: Integer;
  public
    destructor Destroy; override;

    function IndexOf(Name: string): Integer;
    function AddTeam(Name: string): TTeam;
    procedure DeleteTeam(Name: string);

    class function FromJsonString(AJsonString: string): TTeams;

    property Items: TArray<TTeam> read FItems write FItems;
    property Count: Integer read GetCount;
  end;

implementation

uses
  Rest.Json, System.SysUtils;

{ TTeams }

function TTeams.AddTeam(Name: string): TTeam;
var
  Team: TTeam;
begin
  Team := TTeam.Create;
  try
    Team.Name := Name;
    Result := Team;

    SetLength(FItems, Length(FItems)+1);
    FItems[High(FItems)] := Team;
  finally
    FreeAndNil(Team);
  end;
end;

procedure TTeams.DeleteTeam(Name: string);
var
  Idx: Integer;
  ALength: Integer;
  i: Integer;
begin
  Idx := IndexOf(Name);
  if Idx < 0 then
    Exit;

  FItems[Idx].Free;
  ALength := High(FItems);
  if ALength = 0 then
    SetLength(FItems, 0)
  else
  begin
    if Idx < ALength then
      for i := Idx + 1 to ALength do
        FItems[i - 1] := FItems[i];
    SetLength(FItems, ALength);
  end;
end;

destructor TTeams.Destroy;
var
  LItemsItem: TTeam;
begin
  for LItemsItem in FItems do
    LItemsItem.Free;

  inherited;
end;

class function TTeams.FromJsonString(AJsonString: string): TTeams;
begin
  Result := TJson.JsonToObject<TTeams>(AJsonString);
end;

function TTeams.GetCount: Integer;
begin
  Result := High(FItems);
end;

function TTeams.IndexOf(Name: string): Integer;
var
  i: Integer;
begin
  Result := -1;
  for i := 0 to Count do
    if SameText(FItems[i].Name, Name) then
    begin
      Result := i;
      Break;
    end;
end;

{ TTeam }

function TTeam.AddUnit(BaseId, Name: string): TUnitTeam;
var
  UnitT: TUnitTeam;
begin
  UnitT := TUnitTeam.Create;
  try
    UnitT.Base_id := BaseId;
    UnitT.Name := Name;
    Result := UnitT;

    SetLength(FUnits, Length(FUnits)+1);
    FUnits[High(FUnits)] := UnitT;
  finally
    FreeAndNil(UnitT);
  end;
end;

procedure TTeam.DeleteUnit(Name: string);
var
  Idx: Integer;
  ALength: Integer;
  i: Integer;
begin
  Idx := IndexOf(Name);
  if Idx < 0 then
    Exit;

  FUnits[Idx].Free;
  ALength := High(FUnits);
  if ALength = 0 then
    SetLength(FUnits, 0)
  else
  begin
    if Idx < ALength then
      for i := Idx + 1 to ALength do
        FUnits[i - 1] := FUnits[i];
    SetLength(FUnits, ALength);
  end;
end;

destructor TTeam.Destroy;
var
  LunitsItem: TUnitTeam;
begin
 for LunitsItem in FUnits do
   LunitsItem.Free;

  inherited;
end;

class function TTeam.FromJsonString(AJsonString: string): TTeam;
begin
  Result := TJson.JsonToObject<TTeam>(AJsonString);
end;

function TTeam.GetCount: Integer;
begin
  Result := High(FUnits);
end;

function TTeam.IndexOf(Name: string): Integer;
var
  i: Integer;
begin
  Result := -1;
  for i := 0 to Count do
    if SameText(FUnits[i].Name, Name) then
    begin
      Result := i;
      Break;
    end;
end;

{ TUnitTeam }

class function TUnitTeam.FromJsonString(AJsonString: string): TUnitTeam;
begin
  Result := TJson.JsonToObject<TUnitTeam>(AJsonString);
end;

end.
