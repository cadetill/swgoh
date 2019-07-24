unit uGear;

interface

uses
  uBase;

const
  cFileName = 'Gear.json';

type
  TIngredients = class(TBase)
  private
    FAmount: Extended;
    FGear: string;
  public
    class function FromJsonString(AJsonString: string): TIngredients;

    property Amount: Extended read FAmount write FAmount;
    property Gear: string read FGear write FGear;
  end;

  TItem = class(TBase)
  private
    FBase_id: string;
    FCost: Extended;
    FImage: string;
    FIngredients: TArray<TIngredients>;
    FMark: string;
    FName: string;
    FRequired_level: Extended;
    FTier: Extended;
    FUrl: string;
    FAlias: string;
    FToCheck: Boolean;

    function GetCount: Integer;
  public
    destructor Destroy; override;

    class function FromJsonString(AJsonString: string): TItem;

    property Base_id: string read FBase_id write FBase_id;
    property Cost: Extended read FCost write FCost;
    property Image: string read FImage write FImage;
    property Ingredients: TArray<TIngredients> read FIngredients write FIngredients;
    property Mark: string read FMark write FMark;
    property Name: string read FName write FName;
    property Required_level: Extended read FRequired_level write FRequired_level;
    property Tier: Extended read FTier write FTier;
    property Url: string read FUrl write FUrl;
    property Alias: string read FAlias write FAlias;
    property ToCheck: Boolean read FToCheck write FToCheck;
    property Count: Integer read GetCount;
  end;

  TGear = class(TBase)
  private
    FItems: TArray<TItem>;
    function GetCount: Integer;
  public
    destructor Destroy; override;

    function IndexOf(BaseId: string): Integer;
    procedure Compare(FileName: string); override;
    procedure AssignNoDefValues(Origin, Dest: TItem);

    class function FromJsonString(AJsonString: string): TGear;

    property Items: TArray<TItem> read FItems write FItems;
    property Count: Integer read GetCount;
  end;

implementation

uses
  Rest.Json, System.SysUtils, System.Classes, System.IOUtils;

{ TGear }

procedure TGear.AssignNoDefValues(Origin, Dest: TItem);
begin
  Dest.ToCheck := Origin.ToCheck;
  Dest.Alias := Origin.Alias;
end;

procedure TGear.Compare(FileName: string);
var
  OldList: TGear;
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
    OldList := TGear.FromJsonString(L.Text);
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

destructor TGear.Destroy;
var
  LItemsItem: TItem;
begin
  for LItemsItem in FItems do
    LItemsItem.Free;

  inherited;
end;

class function TGear.FromJsonString(AJsonString: string): TGear;
begin
  Result := TJson.JsonToObject<TGear>(AJsonString);
end;

function TGear.GetCount: Integer;
begin
  Result := High(FItems);
end;

function TGear.IndexOf(BaseId: string): Integer;
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

{ TItem }

destructor TItem.Destroy;
var
  LingredientsItem: TIngredients;
begin
 for LingredientsItem in FIngredients do
   LingredientsItem.Free;

  inherited;
end;

class function TItem.FromJsonString(AJsonString: string): TItem;
begin
  Result := TJson.JsonToObject<TItem>(AJsonString);
end;

function TItem.GetCount: Integer;
begin
  Result := High(FIngredients);
end;

{ TIngredients }

class function TIngredients.FromJsonString(AJsonString: string): TIngredients;
begin
  Result := TJson.JsonToObject<TIngredients>(AJsonString);
end;

end.
