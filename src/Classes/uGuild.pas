unit uGuild;

interface

uses
  uPlayer, uBase;

type
  TInfoGuild = class(TBase)
  private
    FGalactic_power: Extended;
    FId: Extended;
    FMember_count: Extended;
    FName: string;
    FProfile_count: Extended;
    FRank: Extended;
  public
    class function FromJsonString(AJsonString: string): TInfoGuild;

    property Galactic_power: Extended read FGalactic_power write FGalactic_power;
    property Id: Extended read FId write FId;
    property Member_count: Extended read FMember_count write FMember_count;
    property Name: string read FName write FName;
    property Profile_count: Extended read FProfile_count write FProfile_count;
    property Rank: Extended read FRank write FRank;
  end;

  TGuild = class(TBase)
  private
    FData: TInfoGuild;
    FPlayers: TArray<TPlayer>;
    function GetCount: Integer;
  public
    constructor Create;
    destructor Destroy; override;

    class function FromJsonString(AJsonString: string): TGuild;

    property Data: TInfoGuild read FData write FData;
    property Players: TArray<TPlayer> read FPlayers write FPlayers;
    property Count: Integer read GetCount;
  end;

implementation

uses
  REST.Json;

{ TInfoGuild }

class function TInfoGuild.FromJsonString(AJsonString: string): TInfoGuild;
begin
  Result := TJson.JsonToObject<TInfoGuild>(AJsonString);
end;

{ TGuild }

constructor TGuild.Create;
begin
  FData := TInfoGuild.Create;
end;

destructor TGuild.Destroy;
var
  LplayersItem: TPlayer;
begin
  for LplayersItem in FPlayers do
    LplayersItem.Free;

  if Assigned(FData) then
    FData.Free;

  inherited;
end;

class function TGuild.FromJsonString(AJsonString: string): TGuild;
begin
  Result := TJson.JsonToObject<TGuild>(AJsonString);
end;

function TGuild.GetCount: Integer;
begin
  Result := High(FPlayers);
end;

end.
