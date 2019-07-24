unit uTeams;

interface

uses
  System.Classes, REST.Json.Types,
  uBase;

const
  cFileName = 'Teams.json';

type
  TZeta = class(TBase)
  private
    FOptional: Boolean;
    FBase_id: string;
  public
    class function FromJsonString(AJsonString: string): TZeta;

    property Base_id: string read FBase_id write FBase_id;
    property Optional: Boolean read FOptional write FOptional;
  end;

  TUnitTeam = class(TBase)
  private
    FBase_id: string;
    FFixed: Boolean;
    FGear: Integer;
    FSpeed: Integer;
    FZetas: TArray<TZeta>;
    FName: string;
    FAlias: string;
    FFisDam: Integer;
    FTenacity: Integer;
    FSpeDam: Integer;
    FHealth: Integer;
    FPG: Integer;

    function GetCount: Integer;
  public
    function IndexOf(Base_id: string): Integer;

    function AddZeta(Base_id: string; Optional: Boolean): Integer;
    procedure DeleteZeta(Base_id: string);

    function GetUnitScore: Integer;
    function GetMaxScore(IsShip: Boolean): Integer;

    class function FromJsonString(AJsonString: string): TUnitTeam;

    property Base_id: string read FBase_id write FBase_id;
    property Name: string read FName write FName;
    property Alias: string read FAlias write FAlias;
    property Fixed: Boolean read FFixed write FFixed;
    property PG: Integer read FPG write FPG;
    property Gear: Integer read FGear write FGear;
    property Speed: Integer read FSpeed write FSpeed;
    property Tenacity: Integer read FTenacity write FTenacity;
    property Health: Integer read FHealth write FHealth;
    property FisDam: Integer read FFisDam write FFisDam;
    property SpeDam: Integer read FSpeDam write FSpeDam;
    property Zetas: TArray<TZeta> read FZetas write FZetas;
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
    FDefTeam: Boolean;
    FIsShip: Boolean;

    function GetCount: Integer;
  public
    constructor Create(OnChangeEvent: TNotifyEvent); virtual;
    destructor Destroy; override;

    class function GetStringPoints: string;
    class function GetPointsPG: Integer;
    class function GetPointsPGKo: Integer;
    class function GetPointsGear: Integer;
    class function GetPointsGearKo: Integer;
    class function GetPointsSpeed: Integer;
    class function GetPointsSpeedKo: Integer;
    class function GetPointsHealth: Integer;
    class function GetPointsHealthKo: Integer;
    class function GetPointsTenacity: Integer;
    class function GetPointsTenacityKo: Integer;
    class function GetPointsFDamage: Integer;
    class function GetPointsFDamageKo: Integer;
    class function GetPointsSDamage: Integer;
    class function GetPointsSDamageKo: Integer;
    class function GetPointsZeta: Integer;
    class function GetPointsZetaKo: Integer;

    function IndexOf(Name: string): Integer;
    function AddUnit(BaseId, Name: string): TUnitTeam;
    function Move(From, Dest: Integer): Boolean; overload;
    function Move(From, Dest: string): Boolean; overload;
    procedure DeleteUnit(Name: string);

    function GetMaxScore: Integer;

    class function FromJsonString(AJsonString: string): TTeam;

    property Name: string read FName write FName;
    property Score: Integer read FScore write FScore;
    property Units: TArray<TUnitTeam> read FUnits write FUnits;
    property Count: Integer read GetCount;
    property DefTeam: Boolean read FDefTeam write FDefTeam;
    property IsShip: Boolean read FIsShip write FIsShip;
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
    function AddTeam(Name: string; IsShip: Boolean; OnChangeEvent: TNotifyEvent): TTeam;
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
  uIniFiles, uGenFunc,
  Rest.Json, System.SysUtils;

{ TTeams }

function TTeams.AddTeam(Name: string; IsShip: Boolean; OnChangeEvent: TNotifyEvent): TTeam;
begin
  Result := nil;
  if IndexOf(Name) <> -1 then
    Exit;

  Result := TTeam.Create(OnChangeEvent);
  Result.Name := Name;
  Result.IsShip := IsShip;

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

function TTeam.GetMaxScore: Integer;
var
  i: Integer;
begin
  Result := 0;
  for i := 0 to Count do
    Result := Result + Units[i].GetMaxScore(FIsShip);
end;

class function TTeam.GetPointsFDamage: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'FDAMAGEOK', 0);
end;

class function TTeam.GetPointsFDamageKo: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'FDAMAGEKO', 0);
end;

class function TTeam.GetPointsGear: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'GEAROK', 0);
end;

class function TTeam.GetPointsGearKo: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'GEARKO', 0);
end;

class function TTeam.GetPointsHealth: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'HEALTHOK', 0);
end;

class function TTeam.GetPointsHealthKo: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'HEALTHKO', 0);
end;

class function TTeam.GetPointsPGKo: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'PGKO', 0);
end;

class function TTeam.GetPointsPG: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'PGOK', 0);
end;

class function TTeam.GetPointsSDamage: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'SDAMAGEOK', 0);
end;

class function TTeam.GetPointsSDamageKo: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'SDAMAGEKO', 0);
end;

class function TTeam.GetPointsSpeed: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'SPEEDOK', 0);
end;

class function TTeam.GetPointsSpeedKo: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'SPEEDKO', 0);
end;

class function TTeam.GetPointsTenacity: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'TENACITYOK', 0);
end;

class function TTeam.GetPointsTenacityKo: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'TENACITYKO', 0);
end;

class function TTeam.GetPointsZeta: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'ZETASOK', 0);
end;

class function TTeam.GetPointsZetaKo: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);
  Result := TFileIni.GetIntValue('TOSUM_TEAMS', 'ZETASKO', 0);
end;

class function TTeam.GetStringPoints: string;
const
  Text = 'if OK / if not OK'#13 +
         '%d/%d points by Gear'#13 +
         '%d/%d points for Speed'#13 +
         '%d/%d point for Health'#13 +
         '%d/%d points for Tenacity'#13 +
         '%d/%d points for Fisical Damage'#13 +
         '%d/%d points for Special Damage'#13 +
         '%d/%d points for Zeta';
begin
  Result := Format(Text, [
                          TTeam.GetPointsGear, TTeam.GetPointsGearKo,
                          TTeam.GetPointsSpeed, TTeam.GetPointsSpeedKo,
                          TTeam.GetPointsHealth, TTeam.GetPointsHealthKo,
                          TTeam.GetPointsTenacity, TTeam.GetPointsTenacityKo,
                          TTeam.GetPointsFDamage, TTeam.GetPointsFDamageKo,
                          TTeam.GetPointsSDamage, TTeam.GetPointsSDamageKo,
                          TTeam.GetPointsZeta, TTeam.GetPointsZetaKo
                         ]);
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

function TUnitTeam.AddZeta(Base_id: string; Optional: Boolean): Integer;
begin
  Result := IndexOf(Base_id);
  if Result <> -1 then
  begin
    FZetas[Result].Optional := Optional;
    Exit;
  end;

  SetLength(FZetas, Length(FZetas)+1);
  FZetas[High(FZetas)] := TZeta.Create;
  FZetas[High(FZetas)].Base_id := Base_id;
  FZetas[High(FZetas)].Optional := Optional;
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

  FZetas[Idx].Free;
  FZetas[Idx] := nil;

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

function TUnitTeam.GetMaxScore(IsShip: Boolean): Integer;
var
  i: Integer;
begin
  Result := 0;
  if not Fixed then
    Exit;

  if IsShip then
    Result := TTeam.GetPointsGear
  else
    Result := ((cMaxLevel - FGear) + 1) * TTeam.GetPointsGear;

  if FPG > 0 then
    Inc(Result, TTeam.GetPointsPG);
  if FSpeed > 0 then
    Inc(Result, TTeam.GetPointsSpeed);
  if FTenacity <> 0 then
    Inc(Result, TTeam.GetPointsTenacity);
  if FHealth <> 0 then
    Inc(Result, TTeam.GetPointsHealth);
  if FFisDam <> 0 then
    Inc(Result, TTeam.GetPointsFDamage);
  if FSpeDam <> 0 then
    Inc(Result, TTeam.GetPointsSDamage);

  for i := 0 to Count do
    Inc(Result, TTeam.GetPointsZeta);
end;

function TUnitTeam.GetUnitScore: Integer;
var
  i: Integer;
begin
  Result := 0;
  if not FFixed then
    Exit;

  Inc(Result, TTeam.GetPointsGear);

  if FPG <> 0 then
    Inc(Result, TTeam.GetPointsPG);
  if FSpeed <> 0 then
    Inc(Result, TTeam.GetPointsSpeed);
  if FTenacity <> 0 then
    Inc(Result, TTeam.GetPointsTenacity);
  if FHealth <> 0 then
    Inc(Result, TTeam.GetPointsHealth);
  if FFisDam <> 0 then
    Inc(Result, TTeam.GetPointsFDamage);
  if FSpeDam <> 0 then
    Inc(Result, TTeam.GetPointsSDamage);

  for i := 0 to Count do
  begin
    if not FZetas[i].Optional then
      Inc(Result, TTeam.GetPointsZeta);
  end;
end;

function TUnitTeam.IndexOf(Base_id: string): Integer;
var
  i: Integer;
begin
  Result := -1;
  for i := 0 to Count do
    if SameText(FZetas[i].Base_id, Base_id) then
    begin
      Result := i;
      Break;
    end;
end;

{ TZeta }

class function TZeta.FromJsonString(AJsonString: string): TZeta;
begin
  Result := TJson.JsonToObject<TZeta>(AJsonString);
end;

end.
