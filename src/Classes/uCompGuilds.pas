unit uCompGuilds;

interface

uses
  uBase;

const
  cFileName = 'CompGuilds.json';

type
  TCompGuild = class(TBase)
  private
    FName: string;
    FBase_id: string;
  public
    class function FromJsonString(AJsonString: string): TCompGuild;

    property Name: string read FName write FName;
    property Base_Id: string read FBase_id write FBase_id;
  end;

  TCompGuilds = class(TBase)
  private
    FItems: TArray<TCompGuild>;
    function GetCount: Integer;
  public
    destructor Destroy; override;

    function IndexOf(BaseId: string): Integer;
    function AddUnit(BaseId, Name: string): TCompGuild;
    function Move(From, Dest: Integer): Boolean; overload;
    function Move(From, Dest: string): Boolean; overload;
    procedure DeleteUnit(Name: string);

    class function FromJsonString(AJsonString: string): TCompGuilds;

    property Items: TArray<TCompGuild> read FItems write FItems;
    property Count: Integer read GetCount;
  end;

implementation

uses
  System.SysUtils,
  REST.Json;

{ TCompGuild }

class function TCompGuild.FromJsonString(AJsonString: string): TCompGuild;
begin
  Result := TJson.JsonToObject<TCompGuild>(AJsonString);
end;

{ TCompGuilds }

function TCompGuilds.AddUnit(BaseId, Name: string): TCompGuild;
begin
  Result := nil;
  if IndexOf(BaseId) <> -1 then
    Exit;

  Result := TCompGuild.Create;
  Result.Base_id := BaseId;
  Result.Name := Name;

  SetLength(FItems, Length(FItems)+1);
  FItems[High(FItems)] := Result;
end;

procedure TCompGuilds.DeleteUnit(Name: string);
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

destructor TCompGuilds.Destroy;
var
  LItemsItem: TCompGuild;
begin
  for LItemsItem in FItems do
    LItemsItem.Free;

  inherited;
end;

class function TCompGuilds.FromJsonString(AJsonString: string): TCompGuilds;
begin
  Result := TJson.JsonToObject<TCompGuilds>(AJsonString);
end;

function TCompGuilds.GetCount: Integer;
begin
  Result := High(FItems);
end;

function TCompGuilds.IndexOf(BaseId: string): Integer;
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

function TCompGuilds.Move(From, Dest: Integer): Boolean;
var
  CG: TCompGuild;
  i: Integer;
begin
  Result := False;
  if (From < 0) or (From > Count) then
    Exit;
  if (Dest < 0) or (Dest > Count) then
    Exit;
  if Dest = From then
    Exit;

  CG := FItems[From];
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
  FItems[Dest] := CG;
  Result := True;
end;

function TCompGuilds.Move(From, Dest: string): Boolean;
var
  IdxFrom: Integer;
  IdxDest: Integer;
begin
  IdxFrom := IndexOf(From);
  IdxDest := IndexOf(Dest);
  Result := Move(IdxFrom, IdxDest);
end;

end.
