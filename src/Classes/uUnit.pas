unit uUnit;

interface

uses
  System.Classes, System.Generics.Collections, System.JSON.Serializers,
  uBase;

const
  ctGL = 'Galactic Legend';

type
  TAbility = class(TBase)
  private
    FName: string;
    FIs_Omega: Boolean;
    FIs_zeta: Boolean;
    FId: string;
    FAbility_tier: Integer;
    FTier_max: Integer;
  public
    class function FromJsonString(AJsonString: string): TAbility;

    property Is_Omega: Boolean read FIs_Omega write FIs_Omega;
    property Is_Zeta: Boolean read FIs_zeta write FIs_zeta;
    property Name: string read FName write FName;
    property Id: string read FId write FId;
    property Ability_tier: Integer read FAbility_tier write FAbility_tier;
    property Tier_max: Integer read FTier_max write FTier_max;
  end;

  TGear_levels = class(TBase)
  private
    FGear: TArray<string>;
    FTier: Extended;

    function GetCount: Integer;
  public
    function IndexOf(BaseId: string): Integer;

    class function FromJsonString(AJsonString: string): TGear_levels;

    property Gear: TArray<string> read FGear write FGear;
    property Tier: Extended read FTier write FTier;
    property Count: Integer read GetCount;
  end;

  TGear = class(TBase)
  private
    FBase_id: string;
    FIs_obtained: Boolean;
    FSlot: Extended;
  public
    class function FromJsonString(AJsonString: string): TGear;

    property Base_id: string read FBase_id write FBase_id;
    property Is_obtained: Boolean read FIs_obtained write FIs_obtained;
    property Slot: Extended read FSlot write FSlot;
  end;

  TStats = class(TBase)
  private
    F1: Extended;
    F10: Extended;
    F11: Extended;
    F12: Extended;
    F13: Extended;
    F14: Extended;
    F15: Extended;
    F16: Extended;
    F17: Extended;
    F18: Extended;
    F2: Extended;
    F27: Extended;
    F28: Extended;
    F3: Extended;
    F37: Extended;
    F38: Extended;
    F39: Extended;
    F4: Extended;
    F40: Extended;
    F5: Extended;
    F6: Extended;
    F7: Extended;
    F8: Extended;
    F9: Extended;
  public
    class function FromJsonString(AJsonString: string): TStats;

    [JsonName('1')]
    property S1: Extended read F1 write F1;
    [JsonName('2')]
    property S2: Extended read F2 write F2;
    [JsonName('3')]
    property S3: Extended read F3 write F3;
    [JsonName('4')]
    property S4: Extended read F4 write F4;
    [JsonName('5')]
    property S5: Extended read F5 write F5;
    [JsonName('6')]
    property S6: Extended read F6 write F6;
    [JsonName('7')]
    property S7: Extended read F7 write F7;
    [JsonName('8')]
    property S8: Extended read F8 write F8;
    [JsonName('9')]
    property S9: Extended read F9 write F9;
    [JsonName('10')]
    property S10: Extended read F10 write F10;
    [JsonName('11')]
    property S11: Extended read F11 write F11;
    [JsonName('12')]
    property S12: Extended read F12 write F12;
    [JsonName('13')]
    property S13: Extended read F13 write F13;
    [JsonName('14')]
    property S14: Extended read F14 write F14;
    [JsonName('15')]
    property S15: Extended read F15 write F15;
    [JsonName('16')]
    property S16: Extended read F16 write F16;
    [JsonName('17')]
    property S17: Extended read F17 write F17;
    [JsonName('18')]
    property S18: Extended read F18 write F18;
    [JsonName('27')]
    property S27: Extended read F27 write F27;
    [JsonName('28')]
    property S28: Extended read F28 write F28;
    [JsonName('37')]
    property S37: Extended read F37 write F37;
    [JsonName('38')]
    property S38: Extended read F38 write F38;
    [JsonName('39')]
    property S39: Extended read F39 write F39;
    [JsonName('40')]
    property S40: Extended read F40 write F40;
  end;

  TUnit = class(TBase)
  private
    FName: string;
    FBase_id: string;
    FUrl: string;
    FPower: Integer;
    FAbilities: TArray<TAbility>;
    FLevel: Integer;
    FRarity: Integer;
    FGear_level: Integer;
    FZeta_abilities: TArray<String>;
    FImage: String;
    FDescription: String;
    FAbility_classes: TArray<String>;
    FMultiplier: Integer;
    FGear_levels: TArray<TGear_levels>;
    FGear: TArray<TGear>;
    FStats: TStats;
    FAlias: string;
    FRelic_tier: Integer;
    FCategories: TArray<String>;
    function GetCountA: Integer;
    function GetCountZ: Integer;
    function GetCountG: Integer;
    function GetCountGL: Integer;
  public
    constructor Create; virtual;
    destructor Destroy; override;

    function IndexOfZ(BaseId: string): Integer;
    function IsLegend: Boolean;

    class function FromJsonString(AJsonString: string): TUnit;

    property Name: string read FName write FName;
    property Base_Id: string read FBase_id write FBase_id;
    property Url: string read FUrl write FUrl;
    property Power: Integer read FPower write FPower;
    property Level: Integer read FLevel write FLevel;
    property Gear: TArray<TGear> read FGear write FGear;
    property Gear_level: Integer read FGear_level write FGear_level;
    property Gear_levels: TArray<TGear_levels> read FGear_levels write FGear_levels;
    property Rarity: Integer read FRarity write FRarity;
    property Abilities: TArray<TAbility> read FAbilities write FAbilities;
    property Zeta_abilities: TArray<String> read FZeta_abilities write FZeta_abilities;
    property Image: String read FImage write FImage;
    property Description: String read FDescription write FDescription;
    property Ability_classes: TArray<String> read FAbility_classes write FAbility_classes;
    property Categories: TArray<String> read FCategories write FCategories;
    property Stats: TStats read FStats write FStats;
    property Multiplier: Integer read FMultiplier write FMultiplier;
    property Alias: string read FAlias write FAlias;
    property Relic_tier: Integer read FRelic_tier write FRelic_tier;
    property CountZ: Integer read GetCountZ;
    property CountA: Integer read GetCountA;
    property CountG: Integer read GetCountG;
    property CountGL: Integer read GetCountGL;
  end;

  TUnitList = class(TBase)
  private
    FItems: TArray<TUnit>;
    function GetCount: Integer;
  public
    destructor Destroy; override;

    function IndexOf(BaseId: string): Integer;
    procedure AssignNoDefValues(Origin, Dest: TUnit);

    class function FromJsonString(AJsonString: string): TUnitList; virtual;

    property Items: TArray<TUnit> read FItems write FItems;
    property Count: Integer read GetCount;
  end;

implementation

uses
  System.SysUtils, System.JSON.Types, REST.Json;

{ TUnit }

constructor TUnit.Create;
begin
  FMultiplier := 1;
  FStats := TStats.Create;
end;

destructor TUnit.Destroy;
var
  Item: TAbility;
  GearLevelItem: TGear_levels;
  LGearItem: TGear;
begin
  for LGearItem in FGear do
    LGearItem.Free;
  for GearLevelItem in FGear_levels do
    GearLevelItem.Free;
  for Item in FAbilities do
    Item.Free;

  FStats.Free;

  inherited;
end;

class function TUnit.FromJsonString(AJsonString: string): TUnit;
begin
  Result := TJson.JsonToObject<TUnit>(AJsonString);
end;

function TUnit.GetCountA: Integer;
begin
  Result := High(FAbilities);
end;

function TUnit.GetCountG: Integer;
begin
  Result := High(FGear);
end;

function TUnit.GetCountGL: Integer;
begin
  Result := High(FGear_levels);
end;

function TUnit.GetCountZ: Integer;
begin
  Result := High(FZeta_abilities);
end;

function TUnit.IndexOfZ(BaseId: string): Integer;
var
  i: Integer;
begin
  Result := -1;
  for i := 0 to CountZ do
    if SameText(FZeta_abilities[i], BaseId) then
    begin
      Result := i;
      Break;
    end;
end;

function TUnit.IsLegend: Boolean;
begin
  Result := IndexOfZ('uniqueskill_GALACTICLEGEND01') <> -1;
end;

{ TAbility }

class function TAbility.FromJsonString(AJsonString: string): TAbility;
begin
  Result := TJson.JsonToObject<TAbility>(AJsonString);
end;

{ TUnitList }

procedure TUnitList.AssignNoDefValues(Origin, Dest: TUnit);
begin
  Dest.Multiplier := Origin.Multiplier;
  Dest.Alias := Origin.Alias;
end;

destructor TUnitList.Destroy;
var
  LItemsItem: TUnit;
begin
 for LItemsItem in FItems do
   LItemsItem.Free;

  inherited;
end;

class function TUnitList.FromJsonString(AJsonString: string): TUnitList;
begin
  Result := TJson.JsonToObject<TUnitList>(AJsonString);
end;

function TUnitList.GetCount: Integer;
begin
  Result := High(FItems);
end;

function TUnitList.IndexOf(BaseId: string): Integer;
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

{ TGear_levels }

class function TGear_levels.FromJsonString(AJsonString: string): TGear_levels;
begin
  Result := TJson.JsonToObject<TGear_levels>(AJsonString);
end;

function TGear_levels.GetCount: Integer;
begin
  Result := High(FGear);
end;

function TGear_levels.IndexOf(BaseId: string): Integer;
var
  i: Integer;
begin
  Result := -1;
  for i := 0 to Count do
    if SameText(FGear[i], BaseId) then
    begin
      Result := i;
      Break;
    end;
end;

{ TGear }

class function TGear.FromJsonString(AJsonString: string): TGear;
begin
  Result := TJson.JsonToObject<TGear>(AJsonString);
end;

{ TStats }

class function TStats.FromJsonString(AJsonString: string): TStats;
begin
  Result := TJson.JsonToObject<TStats>(AJsonString);
end;

end.
