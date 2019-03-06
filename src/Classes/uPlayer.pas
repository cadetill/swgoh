unit uPlayer;

interface

uses
  System.Generics.Collections,
  uBase, uUnit;

type
  TArena = class
  private
    FLeader: string;
    FMembers: TArray<string>;
    FRank: Integer;
  public
    class function FromJsonstring(AJsonstring: string): TArena;

    property Leader: string read FLeader write FLeader;
    property Members: TArray<string> read FMembers write FMembers;
    property Rank: Integer read FRank write FRank;
  end;

  TFleet_arena = class
  private
    FLeader: string;
    FMembers: TArray<string>;
    FRank: Integer;
    FReinforcements: TArray<string>;
  public
    class function FromJsonstring(AJsonstring: string): TFleet_arena;

    property Leader: string read FLeader write FLeader;
    property Members: TArray<string> read FMembers write FMembers;
    property Rank: Integer read FRank write FRank;
    property Reinforcements: TArray<string> read FReinforcements write FReinforcements;
  end;

  TInfoPlayer = class(TBase)
  private
    FAlly_code: Integer;
    FArena_leader_base_id: string;
    FArena_rank: Integer;
    FCharacter_galactic_power: Integer;
    FGalactic_power: Integer;
    FGalactic_war_won: Integer;
    FGuild_contribution: Integer;
    FGuild_exchange_donations: Integer;
    FGuild_id: Integer;
    FGuild_name: string;
    FGuild_raid_won: Integer;
    FLast_updated: string;
    FLevel: Integer;
    FName: string;
    FPve_battles_won: Integer;
    FPve_hard_won: Integer;
    FPvp_battles_won: Integer;
    FShip_battles_won: Integer;
    FShip_galactic_power: Integer;
    FUrl: string;
    FFleet_arena: TFleet_arena;
    FArena: TArena;
  public
    constructor Create; virtual;
    destructor Destroy; override;

    class function FromJsonstring(AJsonstring: string): TInfoPlayer;

    property Ally_code: Integer read FAlly_code write FAlly_code;
    property Arena: TArena read FArena write FArena;
    property Arena_leader_base_id: string read FArena_leader_base_id write FArena_leader_base_id;
    property Arena_rank: Integer read FArena_rank write FArena_rank;
    property Character_galactic_power: Integer read FCharacter_galactic_power write FCharacter_galactic_power;
    property Fleet_arena: TFleet_arena read FFleet_arena write FFleet_arena;
    property Galactic_power: Integer read FGalactic_power write FGalactic_power;
    property Galactic_war_won: Integer read FGalactic_war_won write FGalactic_war_won;
    property Guild_contribution: Integer read FGuild_contribution write FGuild_contribution;
    property Guild_exchange_donations: Integer read FGuild_exchange_donations write FGuild_exchange_donations;
    property Guild_id: Integer read FGuild_id write FGuild_id;
    property Guild_name: string read FGuild_name write FGuild_name;
    property Guild_raid_won: Integer read FGuild_raid_won write FGuild_raid_won;
    property Last_updated: string read FLast_updated write FLast_updated;
    property Level: Integer read FLevel write FLevel;
    property Name: string read FName write FName;
    property Pve_battles_won: Integer read FPve_battles_won write FPve_battles_won;
    property Pve_hard_won: Integer read FPve_hard_won write FPve_hard_won;
    property Pvp_battles_won: Integer read FPvp_battles_won write FPvp_battles_won;
    property Ship_battles_won: Integer read FShip_battles_won write FShip_battles_won;
    property Ship_galactic_power: Integer read FShip_galactic_power write FShip_galactic_power;
    property Url: string read FUrl write FUrl;
  end;

  TUnits = class(TBase)
  private
    FData: TUnit;
  public
    constructor Create; virtual;
    destructor Destroy; override;

    class function FromJsonstring(AJsonstring: string): TUnits;

    property Data: TUnit read FData write FData;
  end;

  TPlayer = class(TBase)
  private
    FData: TInfoPlayer;
    FUnits: TArray<TUnits>;
    function GetCount: Integer;
  public
    constructor Create; virtual;
    destructor Destroy; override;

    function IndexOf(BaseId: string): Integer;

    class function FromJsonstring(AJsonstring: string): TPlayer;

    property Data: TInfoPlayer read FData write FData;
    property Units: TArray<TUnits> read FUnits write FUnits;
    property Count: Integer read GetCount;
  end;

implementation

uses
  System.SysUtils, Rest.Json;

{ TPlayer }

constructor TPlayer.Create;
begin
  inherited;

  FData := TInfoPlayer.Create;
end;

destructor TPlayer.Destroy;
var
  Item: TUnits;
begin
 for Item in FUnits do
   Item.Free;

  if Assigned(FData) then
    FreeAndNil(FData);

  inherited;
end;

class function TPlayer.FromJsonstring(AJsonstring: string): TPlayer;
begin
  Result := TJson.JsonToObject<TPlayer>(AJsonstring);
end;

function TPlayer.GetCount: Integer;
begin
  Result := High(FUnits);
end;

function TPlayer.IndexOf(BaseId: string): Integer;
var
  i: Integer;
begin
  Result := -1;
  for i := 0 to Count do
    if SameText(FUnits[i].Data.Base_Id, BaseId) then
    begin
      Result := i;
      Break;
    end;
end;

{ TInfoPlayer }

constructor TInfoPlayer.Create;
begin
  inherited;

  FFleet_arena := TFleet_arena.Create;
  FArena := TArena.Create;
end;

destructor TInfoPlayer.Destroy;
begin
  if Assigned(FFleet_arena) then
    FreeAndNil(FFleet_arena);
  if Assigned(FArena) then
    FreeAndNil(FArena);

  inherited;
end;

class function TInfoPlayer.FromJsonstring(AJsonstring: string): TInfoPlayer;
begin
  Result := TJson.JsonToObject<TInfoPlayer>(AJsonstring);
end;

{ TUnits }

constructor TUnits.Create;
begin
  inherited;

  FData := TUnit.Create;
end;

destructor TUnits.Destroy;
begin
  if Assigned(FData) then
    FreeAndNil(FData);

  inherited;
end;

class function TUnits.FromJsonstring(AJsonstring: string): TUnits;
begin
  Result := TJson.JsonToObject<TUnits>(AJsonstring);
end;

{ TArena }

class function TArena.FromJsonstring(AJsonstring: string): TArena;
begin
  Result := TJson.JsonToObject<TArena>(AJsonstring);
end;

{ TFleet_arena }

class function TFleet_arena.FromJsonstring(AJsonstring: string): TFleet_arena;
begin
  Result := TJson.JsonToObject<TFleet_arena>(AJsonstring);
end;

end.
