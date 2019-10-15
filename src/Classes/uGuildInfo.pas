unit uGuildInfo;

interface

uses
  Generics.Collections,
  uGuild, uCompGuilds, uGenFunc, uPlayer;

type
  TToonInfo = class
  private
    FGear12: Integer;
    FGear13: Integer;
    FGear12M: Integer;
    FBase_id: string;
  public
    property Base_Id: string read FBase_id write FBase_id;
    property Gear13: Integer read FGear13 write FGear13;
    property Gear12M: Integer read FGear12M write FGear12M;
    property Gear12: Integer read FGear12 write FGear12;
  end;

  TGuildInfo = class
  private
    FToons: TObjectList<TToonInfo>;
    FMods10: Integer;
    FMods25: Integer;
    FMods15: Integer;
    FMods6: Integer;
    FGear12: Integer;
    FGear13: Integer;
    FGear11: Integer;
    FAvgArenaRank: Extended;
    FGalacticPower: Extended;
    FZetas: Integer;
    FAvgFeeltRank: Extended;
    FMods20: Integer;
    FName: string;

    FEndThread: Boolean;
    procedure OnTerminate(Sender: TObject);
    function GetInfoMods(Player: TPlayer): TModsInfo;
  public
    constructor Create;
    destructor Destroy; override;

    procedure GetAvgRank(Html: string);
    procedure GetInfoPlayers(Guild: TGuild; Toons: TCompGuilds);
    function IndexOf(BaseId: string): Integer;

    property Name: string read FName write FName;
    property GalacticPower: Extended read FGalacticPower write FGalacticPower;
    property AvgArenaRank: Extended read FAvgArenaRank write FAvgArenaRank;
    property AvgFeeltRank: Extended read FAvgFeeltRank write FAvgFeeltRank;
    property Gear13: Integer read FGear13 write FGear13;
    property Gear12: Integer read FGear12 write FGear12;
    property Gear11: Integer read FGear11 write FGear11;
    property Zetas: Integer read FZetas write FZetas;
    property Mods6: Integer read FMods6 write FMods6;
    property Mods10: Integer read FMods10 write FMods10;
    property Mods15: Integer read FMods15 write FMods15;
    property Mods20: Integer read FMods20 write FMods20;
    property Mods25: Integer read FMods25 write FMods25;
    property Toons: TObjectList<TToonInfo> read FToons write FToons;
  end;

implementation

uses
  System.SysUtils, System.Classes, System.IOUtils, System.DateUtils,
  uRESTMdl;

{ TGuildInfo }

constructor TGuildInfo.Create;
begin
  FToons := TObjectList<TToonInfo>.Create;
end;

destructor TGuildInfo.Destroy;
begin
  FToons.Free;

  inherited;
end;

procedure TGuildInfo.GetAvgRank(Html: string);
var
  Idx: Integer;
  Idx2: Integer;
  TmpStr: string;
begin
  FAvgArenaRank := 0;
  FAvgFeeltRank := 0;

  Idx := Pos('Raid Points', HTML, 1);
  if Idx <> 0 then
  begin
    Idx := Pos('<div class="stat-item-value">', HTML, Idx+12);
    Idx2 := Pos('</div>', HTML, Idx);
    TmpStr := Copy(HTML, Idx+29, Idx2-(Idx+29));
    TmpStr := StringReplace(TmpStr, '.', ',', []);
    TryStrToFloat(TmpStr, FAvgArenaRank);

    Idx := Pos('<div class="stat-item-value">', HTML, Idx2);
    Idx2 := Pos('</div>', HTML, Idx);
    TmpStr := Copy(HTML, Idx+29, Idx2-(Idx+29));
    TmpStr := StringReplace(TmpStr, '.', ',', []);
    TryStrToFloat(TmpStr, FAvgFeeltRank);
  end;
end;

function TGuildInfo.GetInfoMods(Player: TPlayer): TModsInfo;
var
  Th: TThread;
  FileName: string;
begin
  FileName := Player.Data.Ally_code.ToString + '_mods.json';

  // si no existeix o el fitxer de mods té més de 10 díes, el carreguem
  if not TFile.Exists(FileName) or
     (TFile.Exists(FileName) and (IncDay(TFile.GetLastWriteTime(FileName), 10) < Now)) then
  begin
    Th := TThread.CreateAnonymousThread(procedure
          var
            Mdl: TRESTMdl;
          begin
            // realitzem procés
            Mdl := TRESTMdl.Create(nil);
            try
              Mdl.LoadData(tcMods, Player.Data.Ally_code.ToString);
            finally
              FreeAndNil(Mdl);
            end;
            FEndThread := True;
          end
          );
    Th.OnTerminate := OnTerminate;
    FEndThread := False;
    Th.Start;
    repeat
      Sleep(5);
    until FEndThread;
  end;

  Result := TGenFunc.CheckMods(Player.Data.Ally_code.ToString);
end;

procedure TGuildInfo.GetInfoPlayers(Guild: TGuild; Toons: TCompGuilds);
var
  i,j: Integer;
  ModsInfo: TModsInfo;
//  InfoToon: TToonInfo;
  Idx: Integer;
begin
  FGear13 := 0;
  FGear12 := 0;
  FGear11 := 0;
  FZetas := 0;
  FMods6 := 0;
  FMods10 := 0;
  FMods15 := 0;
  FMods20 := 0;
  FMods25 := 0;

  for i := 0 to Guild.Count do
  begin
    // per cada jugador, mirem mods
    ModsInfo := GetInfoMods(Guild.Players[i]);
    Inc(FMods10, ModsInfo.Plus10);
    Inc(FMods15, ModsInfo.Plus15);
    Inc(FMods20, ModsInfo.Plus20);
    Inc(FMods25, ModsInfo.Plus25);
    Inc(FMods6, ModsInfo.Mods6);

    // per cada jugador, mirem gear, toons i zetes
    for j := 0 to Guild.Players[i].Count do
    begin
      // mirem gear de toons
      case Guild.Players[i].Units[j].Data.Gear_level of
        13: Inc(FGear13);
        12: Inc(FGear12);
        11: Inc(FGear11);
      end;

      // controlem les zetes
      if Guild.Players[i].Units[j].Data.CountZ <> -1 then
        Inc(FZetas, Guild.Players[i].Units[j].Data.CountZ);

      // mirem si el toon cal tenir-ho en compte
      if Toons.IndexOf(Guild.Players[i].Units[j].Data.Base_Id) <> -1 then
      begin
        Idx := IndexOf(Guild.Players[i].Units[j].Data.Base_Id);
        if Idx = -1 then // si no existeix la entrada, la creem
        begin
          Idx := FToons.Add(TToonInfo.Create);
          FToons[Idx].Base_Id := Guild.Players[i].Units[j].Data.Base_Id;
        end;

        case Guild.Players[i].Units[j].Data.Gear_level of
          13: FToons[Idx].Gear13 := FToons[Idx].Gear13 + 1;
          12: FToons[Idx].Gear12 := FToons[Idx].Gear12 + 1;
        end;
      end;
    end;
  end;
end;

function TGuildInfo.IndexOf(BaseId: string): Integer;
var
  i: Integer;
begin
  Result := -1;
  for i := 0 to FToons.Count - 1 do
    if SameText(FToons[i].Base_Id, BaseId) then
    begin
      Result := i;
      Break;
    end;
end;

procedure TGuildInfo.OnTerminate(Sender: TObject);
begin
  FEndThread := True;
end;

end.
