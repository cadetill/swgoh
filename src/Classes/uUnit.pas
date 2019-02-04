unit uUnit;

interface

uses
  System.Classes, System.Generics.Collections,
  uBase;

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
  public
    class function FromJsonString(AJsonString: string): TGear_levels;

    property Gear: TArray<string> read FGear write FGear;
    property Tier: Extended read FTier write FTier;
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
    function GetCountA: Integer;
    function GetCountZ: Integer;
    function GetCountG: Integer;
    function GetCountGL: Integer;
  public
    constructor Create; virtual;
    destructor Destroy; override;

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
    property Multiplier: Integer read FMultiplier write FMultiplier;
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
  System.SysUtils, System.JSON.Serializers, System.JSON.Types, REST.Json;

{ TUnit }

constructor TUnit.Create;
begin
  FMultiplier := 1;
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

{ TAbility }

class function TAbility.FromJsonString(AJsonString: string): TAbility;
begin
  Result := TJson.JsonToObject<TAbility>(AJsonString);
end;

{ TUnitList }

procedure TUnitList.AssignNoDefValues(Origin, Dest: TUnit);
begin
  Dest.Multiplier := Origin.Multiplier;
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

{ TGear }

class function TGear.FromJsonString(AJsonString: string): TGear;
begin
  Result := TJson.JsonToObject<TGear>(AJsonString);
end;

end.
