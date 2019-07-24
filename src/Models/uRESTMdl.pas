unit uRESTMdl;

interface

uses
  System.SysUtils, System.Classes, REST.Types, REST.Client,
  Data.Bind.Components, Data.Bind.ObjectScope, IPPeerClient;

const
  cUrlCharacters = 'https://swgoh.gg/api/characters/';
  cUrlShips = 'https://swgoh.gg/api/ships/';
  cUrlAbilities = 'https://swgoh.gg/api/abilities/';
  cUrlGuild = 'https://swgoh.gg/api/guild/%s/?include_arena=1&t=whatever';
  cUrlPlayer = 'https://swgoh.gg/api/player/%s/?t=whatever';
  cUrlMods = 'https://swgoh.gg/api/players/%s/mods/';
  cUrlGear = 'https://swgoh.gg/api/gear/';

type
  TTypeClass = (tcUnits, tcPlayer, tcGuild, tcAbilities, tcCharacters, tcShips,
    tcMods, tcGear, tcURL);

  TRESTMdl = class(TDataModule)
    RESTClient1: TRESTClient;
    RESTRequest1: TRESTRequest;
    RESTResponse1: TRESTResponse;
  private
    function GetHTML(Url: string; out HTML: string): Boolean;
  public
    function LoadData(TypeClass: TTypeClass; ExtraData: string = ''): string;
  end;

var
  RESTMdl: TRESTMdl;

implementation

uses
  System.IOUtils, System.Net.HttpClientComponent, System.Net.HttpClient,
  uBase, uPlayer, uGuild, uAbilities, uCharacter, uShips, uMods, uGear, uGenFunc;

{%CLASSGROUP 'FMX.Controls.TControl'}

{$R *.dfm}

{ TRESTMdl }

function TRESTMdl.GetHTML(Url: string; out HTML: string): Boolean;
var
  Client: TNetHTTPClient;
  Request: TNetHTTPRequest;
  Resp: IHTTPResponse;
begin
  HTML := '';
  Client := nil;
  Request := nil;
  try
    Client := TNetHTTPClient.Create(nil);
    Request := TNetHTTPRequest.Create(nil);

    Request.Client := Client;
    Request.URL := Url;
    Request.MethodString := 'GET';
    Resp := Request.Execute;
    Result := Resp.StatusCode = 200;
    if Result then
      HTML := Resp.ContentAsString;
  finally
    FreeAndNil(Client);
    FreeAndNil(Request);
  end;
end;

function TRESTMdl.LoadData(TypeClass: TTypeClass; ExtraData: string): string;
var
  Json: string;
  List: TBase;
  Url: string;
  FileName: string;
begin
  Url := '';
  case TypeClass of
    tcUnits:
      begin
        Result := LoadData(tcCharacters);
        if Result = '' then
          Result := LoadData(tcShips);
        if Result = '' then
          Result := LoadData(tcAbilities);
        if Result = '' then
          Result := LoadData(tcGear);
        Exit;
      end;
    tcPlayer:
      begin
        if ExtraData = '' then
          Exit;
        Url := Format(cUrlPlayer, [ExtraData]);
        FileName := ExtraData + '.json';
      end;
    tcGuild:
      begin
        if ExtraData = '' then
          Exit;
        Url := Format(cUrlGuild, [ExtraData]);
        FileName := ExtraData + '_guild.json';
        if TFile.Exists(FileName) then
          TFile.Delete(FileName);
      end;
    tcMods:
      begin
        if ExtraData = '' then
          Exit;
        Url := Format(cUrlMods, [ExtraData]);
        FileName := ExtraData + '_mods.json';
      end;
    tcAbilities:
      begin
        Url := cUrlAbilities;
        FileName := TGenFunc.GetBaseFolder + uAbilities.cFileName;
      end;
    tcCharacters:
      begin
        Url := cUrlCharacters;
        FileName := TGenFunc.GetBaseFolder + uCharacter.cFileName;
      end;
    tcShips:
      begin
        Url := cUrlShips;
        FileName := TGenFunc.GetBaseFolder + uShips.cFileName;
      end;
    tcGear:
      begin
        Url := cUrlGear;
        FileName := TGenFunc.GetBaseFolder + uGear.cFileName;
      end;
    tcURL:
      begin
        if ExtraData = '' then
          Exit;
        if not GetHTML(ExtraData, Result) then
          Result := '-1';
        Exit;
      end;
  end;

  Result := '';
  RESTClient1.BaseURL := Url;
  RESTRequest1.Execute;

  if RESTResponse1.StatusCode <> 200 then
  begin
    Result := 'Error getting data';
    Exit;
  end;

  Json := RESTResponse1.JSONText;
  if Json[1] = '[' then
    Json := '{"Items":' + Json + '}';

  case TypeClass of
    tcPlayer:
      List := TPlayer.FromJsonString(Json);
    tcGuild:
      List := TGuild.FromJsonString(Json);
    tcAbilities:
      List := TAbilities.FromJsonString(Json);
    tcCharacters:
      List := TCharacters.FromJsonString(Json);
    tcShips:
      List := TShips.FromJsonString(Json);
    tcMods:
      List := TMods.FromJsonString(Json);
    tcGear:
      List := TGear.FromJsonString(Json);
  else
    List := nil;
  end;

  if not Assigned(List) then
    Exit;

  // comparem amb el fitxer ja existent recuperant valors propis
  List.Compare(FileName);

  // grabem novament el fitxer
  List.SaveToFile(FileName);
end;

end.
