unit uMods;

interface

uses
  Generics.Collections, Rest.Json,
  uBase;

type
  TPrimary_stat = class(TBase)
  private
    FDisplay_value: string;
    FName: string;
    FStat_id: Extended;
    FValue: Extended;
  public
    class function FromJsonString(AJsonString: string): TPrimary_stat;

    property Display_value: string read FDisplay_value write FDisplay_value;
    property Name: string read FName write FName;
    property Stat_id: Extended read FStat_id write FStat_id;
    property Value: Extended read FValue write FValue;
  end;

  TSecondary_stats = class(TBase)
  private
    FDisplay_value: string;
    FName: string;
    FRoll: Extended;
    FStat_id: Extended;
    FValue: Extended;
  public
    class function FromJsonString(AJsonString: string): TSecondary_stats;

    property Display_value: string read FDisplay_value write FDisplay_value;
    property Name: string read FName write FName;
    property Roll: Extended read FRoll write FRoll;
    property Stat_id: Extended read FStat_id write FStat_id;
    property Value: Extended read FValue write FValue;
  end;

  TMod = class(TBase)
  private
    FCharacter: string;
    FId: string;
    FLevel: Extended;
    FPrimary_stat: TPrimary_stat;
    FRarity: Extended;
    FSecondary_stats: TArray<TSecondary_stats>;
    FSet: Extended;
    FSlot: Extended;
    FTier: Extended;
    function GetCount: Integer;
  public
    constructor Create; virtual;
    destructor Destroy; override;

    class function FromJsonString(AJsonString: string): TMod;

    property Character: string read FCharacter write FCharacter;
    property Id: string read FId write FId;
    property Level: Extended read FLevel write FLevel;
    property Primary_stat: TPrimary_stat read FPrimary_stat write FPrimary_stat;
    property Rarity: Extended read FRarity write FRarity;
    property Secondary_stats: TArray<TSecondary_stats> read FSecondary_stats write FSecondary_stats;
    property &Set: Extended read FSet write FSet;
    property Slot: Extended read FSlot write FSlot;
    property Tier: Extended read FTier write FTier;
    property Count: Integer read GetCount;
  end;

  TMods = class(TBase)
  private
    FMods: TArray<TMod>;
    function GetCount: Integer;
  public
    destructor Destroy; override;

    class function FromJsonString(AJsonString: string): TMods;

    property Mods: TArray<TMod> read FMods write FMods;
    property Count: Integer read GetCount;
  end;

implementation

{ TMods }

destructor TMods.Destroy;
var
  LmodsItem: TMod;
begin
 for LmodsItem in FMods do
   LmodsItem.Free;

  inherited;
end;

class function TMods.FromJsonString(AJsonString: string): TMods;
begin
  Result := TJson.JsonToObject<TMods>(AJsonString);
end;

function TMods.GetCount: Integer;
begin
  Result := High(FMods);
end;

{ TMod }

constructor TMod.Create;
begin
  FPrimary_stat := TPrimary_stat.Create;
end;

destructor TMod.Destroy;
var
  Lsecondary_statsItem: TSecondary_stats;
begin
  for Lsecondary_statsItem in FSecondary_stats do
    Lsecondary_statsItem.Free;

  if Assigned(FPrimary_stat) then
    FPrimary_stat.Free;

  inherited;
end;

class function TMod.FromJsonString(AJsonString: string): TMod;
begin
  Result := TJson.JsonToObject<TMod>(AJsonString);
end;

function TMod.GetCount: Integer;
begin
  Result := High(FSecondary_stats);
end;

{ TSecondary_stats }

class function TSecondary_stats.FromJsonString(
  AJsonString: string): TSecondary_stats;
begin
  Result := TJson.JsonToObject<TSecondary_stats>(AJsonString);
end;

{ TPrimary_stat }

class function TPrimary_stat.FromJsonString(AJsonString: string): TPrimary_stat;
begin
  Result := TJson.JsonToObject<TPrimary_stat>(AJsonString);
end;

end.

