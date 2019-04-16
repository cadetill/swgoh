unit uTeams;

interface

uses
  System.Classes, REST.Json.Types,
  uBase;

const
  cFileName = 'Teams.json';

type
  TUnitTeam = class(TBase)
  private
    FBase_id: string;
    FFixed: Boolean;
    FGear: Integer;
    FSpeed: Integer;
    FStars: Integer;
    FZetas: TArray<string>;
    FName: string;
    FAlias: string;
    function GetCount: Integer;
  public
    function IndexOf(Base_id: string): Integer;
    function AddZeta(Base_id: string): Integer;
    procedure DeleteZeta(Base_id: string);

    class function FromJsonString(AJsonString: string): TUnitTeam;

    property Base_id: string read FBase_id write FBase_id;
    property Name: string read FName write FName;
    property Alias: string read FAlias write FAlias;
    property Fixed: Boolean read FFixed write FFixed;
    property Gear: Integer read FGear write FGear;
    property Speed: Integer read FSpeed write FSpeed;
    property Stars: Integer read FStars write FStars;
    property Zetas: TArray<string> read FZetas write FZetas;
    property Count: Integer read GetCount;
  end;

  TTeam = class(TBase)
  private
    FName: string;
    FUnits: TArray<TUnitTeam>;
    [JSONMarshalledAttribute(False)]
    FOnChange: TNotifyEvent;
    [JSONMarshalledAttribute(False)]
    FTag: Integer;
    FScore: Integer;

    function GetCount: Integer;
  public
    constructor Create(OnChangeEvent: TNotifyEvent); virtual;
    destructor Destroy; override;

    function GetStringPoints: string;
    function GetPointsG12: Integer;
    function GetPointsG11: Integer;
    function GetPointsG10: Integer;
    function GetPointsZeta: Integer;
    function GetPointsSpeed: Integer;

    function IndexOf(Name: string): Integer;
    function AddUnit(BaseId, Name: string): TUnitTeam;
    function Move(From, Dest: Integer): Boolean; overload;
    function Move(From, Dest: string): Boolean; overload;
    procedure DeleteUnit(Name: string);

    class function FromJsonString(AJsonString: string): TTeam;

    property Name: string read FName write FName;
    property Score: Integer read FScore write FScore;
    property Units: TArray<TUnitTeam> read FUnits write FUnits;
    property Count: Integer read GetCount;
    property OnChange: TNotifyEvent read FOnChange write FOnChange;
    property Tag: Integer read FTag write FTag;
  end;

  TTeams = class(TBase)
  private
    FItems: TArray<TTeam>;
    function GetCount: Integer;
  public
    destructor Destroy; override;

    function IndexOf(Name: string): Integer;
    function AddTeam(Name: string; OnChangeEvent: TNotifyEvent): TTeam;
    function Move(From, Dest: Integer): Boolean; overload;
    function Move(From, Dest: string): Boolean; overload;
    procedure DeleteTeam(Name: string);
    procedure Clear;

    class function FromJsonString(AJsonString: string): TTeams;

    property Items: TArray<TTeam> read FItems write FItems;
    property Count: Integer read GetCount;
  end;

implementation

uses
  Rest.Json, System.SysUtils;

{ TTeams }

function TTeams.AddTeam(Name: string; OnChangeEvent: TNotifyEvent): TTeam;
begin
  Result := nil;
  if IndexOf(Name) <> -1 then
    Exit;

  Result := TTeam.Create(OnChangeEvent);
  Result.Name := Name;

  SetLength(FItems, Length(FItems)+1);
  FItems[High(FItems)] := Result;
end;

procedure TTeams.Clear;
var
  i: Integer;
begin
  for i := 0 to Count do
    FItems[i].Free;
  SetLength(FItems, 0);
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

function TTeams.Move(From, Dest: string): Boolean;
var
  IdxFrom: Integer;
  IdxDest: Integer;
begin
  IdxFrom := IndexOf(From);
  IdxDest := IndexOf(Dest);
  Result := Move(IdxFrom, IdxDest);
end;

function TTeams.Move(From, Dest: Integer): Boolean;
var
  Team: TTeam;
  i: Integer;
begin
  Result := False;
  if (From < 0) or (From > Count) then
    Exit;
  if (Dest < 0) or (Dest > Count) then
    Exit;
  if Dest = From then
    Exit;

  Team := FItems[From];
  if From < Dest then
  begin
    for i := From + 1 to Dest do
      FItems[i-1] := FItems[i];
  end
  else
  begin
    for i := From - 1 downto Dest do
      FItems[i+1] := FItems[i];
  end;
  FItems[Dest] := Team;
  Result := True;
end;

{ TTeam }

function TTeam.AddUnit(BaseId, Name: string): TUnitTeam;
begin
  Result := nil;
  if IndexOf(Name) <> -1 then
    Exit;

  Result := TUnitTeam.Create;
  Result.Base_id := BaseId;
  Result.Name := Name;

  SetLength(FUnits, Length(FUnits)+1);
  FUnits[High(FUnits)] := Result;
end;

constructor TTeam.Create(OnChangeEvent: TNotifyEvent);
begin
  FOnChange := OnChangeEvent;
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

function TTeam.GetPointsG10: Integer;
begin
  Result := 1;
end;

function TTeam.GetPointsG11: Integer;
begin
  Result := 2;
end;

function TTeam.GetPointsG12: Integer;
begin
  Result := 3;
end;

function TTeam.GetPointsSpeed: Integer;
begin
  Result := 2;
end;

function TTeam.GetPointsZeta: Integer;
begin
  Result := 2;
end;

function TTeam.GetStringPoints: string;
const
  Text = '%d points g XII'#13'%d points g XI'#13'%d point g X'#13'%d points for Z'#13'%d points for speed';
begin
  Result := Format(Text, [GetPointsG12,
                          GetPointsG11,
                          GetPointsG10,
                          GetPointsZeta,
                          GetPointsSpeed]);
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

function TTeam.Move(From, Dest: string): Boolean;
var
  IdxFrom: Integer;
  IdxDest: Integer;
begin
  IdxFrom := IndexOf(From);
  IdxDest := IndexOf(Dest);
  Result := Move(IdxFrom, IdxDest);
end;

function TTeam.Move(From, Dest: Integer): Boolean;
var
  Units: TUnitTeam;
  i: Integer;
begin
  Result := False;
  if (From < 0) or (From > Count) then
    Exit;
  if (Dest < 0) or (Dest > Count) then
    Exit;
  if Dest = From then
    Exit;

  Units := FUnits[From];
  if From < Dest then
  begin
    for i := From + 1 to Dest do
      FUnits[i-1] := FUnits[i];
  end
  else
  begin
    for i := From - 1 downto Dest do
      FUnits[i+1] := FUnits[i];
  end;
  FUnits[Dest] := Units;
  Result := True;
end;

{ TUnitTeam }

function TUnitTeam.AddZeta(Base_id: string): Integer;
begin
  Result := IndexOf(Base_id);
  if Result <> -1 then
    Exit;

  SetLength(FZetas, Length(FZetas)+1);
  FZetas[High(FZetas)] := Base_id;
  Result := High(FZetas);
end;

procedure TUnitTeam.DeleteZeta(Base_id: string);
var
  Idx: Integer;
  ALength: Integer;
  i: Integer;
begin
  Idx := IndexOf(Base_id);
  if Idx = -1 then
    Exit;

  FZetas[Idx] := '';
  ALength := High(FZetas);
  if ALength = 0 then
    SetLength(FZetas, 0)
  else
  begin
    if Idx < ALength then
      for i := Idx + 1 to ALength do
        FZetas[i - 1] := FZetas[i];
    SetLength(FZetas, ALength);
  end;
end;

class function TUnitTeam.FromJsonString(AJsonString: string): TUnitTeam;
begin
  Result := TJson.JsonToObject<TUnitTeam>(AJsonString);
end;

function TUnitTeam.GetCount: Integer;
begin
  Result := High(FZetas);
end;

function TUnitTeam.IndexOf(Base_id: string): Integer;
var
  i: Integer;
begin
  Result := -1;
  for i := 0 to Count do
    if SameText(FZetas[i], Base_id) then
    begin
      Result := i;
      Break;
    end;
end;

end.
