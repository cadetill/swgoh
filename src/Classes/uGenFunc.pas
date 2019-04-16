unit uGenFunc;

interface

uses
  uPlayer, uUnit, uTeams,
  FMX.ListBox,
  System.Classes;

type
  TPlayerInfo = record
    Power: Double;
    Gear12: Integer;
    Gear11: Integer;
    Gear10: Integer;
    Gear9: Integer;
    Gear8: Integer;
    Zetas: Integer;
    CharRank: Integer;
    ShipRank: Integer;
  end;

  TModsInfo = record
    Arrows: Integer;
    Plus20: Integer;
    Plus15: Integer;
    Plus10: Integer;
    Mods6: Integer;
  end;

  TGenFunc = record
  public
    class function GetIniName: string; static;
    class function GetField(S: string; FieldIndex: Integer; Delimiter: Char): string; static;
    class function CheckPlayer(Player: TPlayer; Char: TUnitList): TPlayerInfo; static;
    class function CheckMods(PlayerId: string): TModsInfo; static;
    class procedure QuickSort(var A: array of Integer; iLo, iHi: Integer); static;
    class procedure GetDefinedTeams(LB: TListBox; var Teams: TTeams; OnChangeTeam, OnClickBEdit, OnClickBDel: TNotifyEvent); static;
    class procedure CopyToClipboard(Text: string); static;
    class function GetBaseFolder: string; static;
  end;

implementation

uses
  uIniFiles, uMods, uMessage,
  System.IOUtils, System.SysUtils,
  FMX.StdCtrls, FMX.Types, FMX.Platform;

{ TGenFunc }

class function TGenFunc.CheckMods(PlayerId: string): TModsInfo;
var
  L: TStringList;
  M: TMods;
  i, j: Integer;
begin
  // inicialitzem valors
  Result.Arrows := 0;
  Result.Plus20 := 0;
  Result.Plus15 := 0;
  Result.Plus10 := 0;
  Result.Mods6 := 0;

  // carreguem mods
  if not TFile.Exists(PlayerId + '_mods.json') then
    Exit;

  L := TStringList.Create;
  try
    L.LoadFromFile(PlayerId + '_mods.json');
    M := TMods.FromJsonString(L.Text);
  finally
    FreeAndNil(L);
  end;

  // fem control de mods
  for i := 0 to M.Count do
  begin
    if M.Mods[i].Rarity = 6 then
      Inc(Result.Mods6);

    if SameText(M.Mods[i].Primary_stat.Name, 'Speed') then
    begin
      Inc(Result.Arrows);
      Continue;
    end;

    for j := 0 to M.Mods[i].Count do
    begin
      if SameText(M.Mods[i].Secondary_stats[j].Name, 'Speed') then
      begin
        if M.Mods[i].Secondary_stats[j].Display_value.ToInteger >= 20 then
          Inc(Result.Plus20)
        else
          if (M.Mods[i].Secondary_stats[j].Display_value.ToInteger >= 15) and
             (M.Mods[i].Secondary_stats[j].Display_value.ToInteger < 20) then
            Inc(Result.Plus15)
          else
            if (M.Mods[i].Secondary_stats[j].Display_value.ToInteger >= 10) and
               (M.Mods[i].Secondary_stats[j].Display_value.ToInteger < 15) then
              Inc(Result.Plus10);
      end;
    end;
  end;
end;

class function TGenFunc.CheckPlayer(Player: TPlayer; Char: TUnitList): TPlayerInfo;
var
  i: Integer;
  Idx: Integer;
  CristalChar: Integer;
  CristalShip: Integer;
begin
  TFileIni.SetFileIni(TGenFunc.GetIniName);

  Result.Power := 0;
  Result.Gear12 := 0;
  Result.Gear11 := 0;
  Result.Gear10 := 0;
  Result.Gear9 := 0;
  Result.Gear8 := 0;
  Result.Zetas := 0;
  for i := 0 to Player.Count do
  begin
    Idx := Char.IndexOf(Player.Units[i].Data.Base_Id);
    if Idx = -1 then
      Continue;

    case Player.Units[i].Data.Gear_level of
      12: Inc(Result.Gear12);
      11: Inc(Result.Gear11);
      10: Inc(Result.Gear10);
      9: Inc(Result.Gear9);
      8: Inc(Result.Gear8);
    end;

    Result.Zetas := Result.Zetas + Player.Units[i].Data.CountZ + 1;

    Result.Power := Result.Power + (Player.Units[i].Data.Power * Char.Items[Idx].Multiplier);
  end;

  Result.Power := Result.Power + (Result.Gear12 * TFileIni.GetIntValue('TOSUM', 'GEARXII', 0)) +
                                 (Result.Gear11 * TFileIni.GetIntValue('TOSUM', 'GEARXI', 0)) +
                                 (Result.Gear10 * TFileIni.GetIntValue('TOSUM', 'GEARX', 0)) +
                                 (Result.Gear9 * TFileIni.GetIntValue('TOSUM', 'GEARIX', 0)) +
                                 (Result.Gear8 * TFileIni.GetIntValue('TOSUM', 'GEARVIII', 0)) +
                                 (Result.Zetas * TFileIni.GetIntValue('TOSUM', 'ZETAS', 0));
  Result.CharRank := 0;
  if Assigned(Player.Data.Arena) then
    Result.CharRank := Player.Data.Arena.Rank;
  case Result.CharRank of
    1: CristalChar := 500;
    2: CristalChar := 450;
    3: CristalChar := 400;
    4: CristalChar := 350;
    5: CristalChar := 300;
    6..10: CristalChar := 250;
    11..20: CristalChar := 200;
    21..50: CristalChar := 150;
    51..100: CristalChar := 100;
    101..200: CristalChar := 75;
    201..500: CristalChar := 60;
    501..1000: CristalChar := 50;
    1001..2500: CristalChar := 40;
    2501..5000: CristalChar := 35;
    5001..10000: CristalChar := 25;
    10001..999999: CristalChar := 15;
  else
    CristalChar := 0;
  end;

  Result.ShipRank := 0;
  if Assigned(Player.Data.Fleet_arena) then
    Result.ShipRank := Player.Data.Fleet_arena.Rank;
  case Result.ShipRank of
    1: CristalShip := 400;
    2: CristalShip := 375;
    3: CristalShip := 350;
    4: CristalShip := 325;
    5: CristalShip := 300;
    6..10: CristalShip := 200;
    11..20: CristalShip := 100;
    21..50: CristalShip := 50;
  else
    CristalShip := 0;
  end;

  case (CristalChar + CristalShip) of
    701..900: Result.Power := Result.Power * TFileIni.GetFloatValue('TOSUM', '900_701', 0);
    551..700: Result.Power := Result.Power * TFileIni.GetFloatValue('TOSUM', '700_551', 0);
    301..550: Result.Power := Result.Power * TFileIni.GetFloatValue('TOSUM', '550_301', 0);
    201..300: Result.Power := Result.Power * TFileIni.GetFloatValue('TOSUM', '300_201', 0);
    151..200: Result.Power := Result.Power * TFileIni.GetFloatValue('TOSUM', '200_151', 0);
    101..150: Result.Power := Result.Power * TFileIni.GetFloatValue('TOSUM', '150_101', 0);
    0..100: Result.Power := Result.Power * TFileIni.GetFloatValue('TOSUM', '100_0', 0);
  end;
end;

class procedure TGenFunc.CopyToClipboard(Text: string);
var
  Svc: IFMXClipboardService;
begin
  if TPlatformServices.Current.SupportsPlatformService(IFMXClipboardService, Svc) then
  begin
    Svc.SetClipboard(Text);

    TMessage.Show('Text copied to ClipBoard');
  end;
end;

class function TGenFunc.GetBaseFolder: string;
begin
  {$IFDEF MSWINDOWS}
  Result := IncludeTrailingPathDelimiter(TPath.GetDirectoryName(ParamStr(0)));
  {$ELSE}
    {$IFDEF ANDROID}
  Result := TPath.Combine(TPath.GetDocumentsPath, IncludeTrailingPathDelimiter('SWGOHApp'));
    {$ELSE}
  Result := TPath.Combine(TPath.GetSharedDownloadsPath, IncludeTrailingPathDelimiter('SWGOHApp'));
    {$ENDIF}
  {$ENDIF}
  if not TDirectory.Exists(TPath.GetDirectoryName(Result)) then
    TDirectory.CreateDirectory(TPath.GetDirectoryName(Result))
end;

class procedure TGenFunc.GetDefinedTeams(LB: TListBox; var Teams: TTeams;
  OnChangeTeam, OnClickBEdit, OnClickBDel: TNotifyEvent);
var
  L: TStringList;
  lbItem: TListBoxItem;
  i: Integer;
  j: Integer;
  BDel: TButton;
  BEdit: TButton;
  Fixed: string;
  NoFix: string;
begin
  LB.Clear;

  if not Assigned(Teams) then
    Exit;

  Teams.Clear;

  if not TFile.Exists(TGenFunc.GetBaseFolder + uTeams.cFileName) then
    Exit;

  // carreguem Json
  L := TStringList.Create;
  try
    L.LoadFromFile(TGenFunc.GetBaseFolder + uTeams.cFileName);
    Teams := TTeams.FromJsonString(L.Text);
  finally
    FreeAndNil(L);
  end;

  // creem TListBox
  for i := 0 to Teams.Count do
  begin
    Teams.Items[i].OnChange := OnChangeTeam;

    lbItem := TListBoxItem.Create(LB);
    lbItem.Text := Teams.Items[i].Name;
    lbItem.IsChecked := True;

    lbItem.ItemData.Detail := '';
    Fixed := '';
    NoFix := '';
    for j := 0 to Teams.Items[i].Count do
    begin
      if Teams.Items[i].Units[j].Fixed then
      begin
        if Fixed <> '' then Fixed := Fixed + ' / ';
        if Teams.Items[i].Units[j].Alias = '' then
          Fixed := Fixed + Teams.Items[i].Units[j].Name
        else
          Fixed := Fixed + Teams.Items[i].Units[j].Alias;
      end
      else
      begin
        if NoFix <> '' then NoFix := NoFix + ' / ';
        if Teams.Items[i].Units[j].Alias = '' then
          NoFix := NoFix + '*' + Teams.Items[i].Units[j].Name
        else
          NoFix := NoFix + '*' + Teams.Items[i].Units[j].Alias;
      end;
    end;
    lbItem.ItemData.Detail := Fixed;
    if (lbItem.ItemData.Detail <> '') and (NoFix <> '') then
      lbItem.ItemData.Detail := lbItem.ItemData.Detail + ' / ';
    lbItem.ItemData.Detail := lbItem.ItemData.Detail + NoFix;

    if not LB.ShowCheckboxes then
    begin
      BDel := TButton.Create(lbItem);
      BDel.Align := TAlignLayout.Right;
      BDel.Width := 40;
      BDel.StyleLookup := 'trashtoolbutton';
      BDel.Parent := lbItem;
      BDel.OnClick := OnClickBDel;

      BEdit := TButton.Create(lbItem);
      BEdit.Align := TAlignLayout.Right;
      BEdit.Width := 40;
      BEdit.StyleLookup := 'composetoolbutton';
      BEdit.Parent := lbItem;
      BEdit.OnClick := OnClickBEdit;
    end;

    LB.AddObject(lbItem);
  end;
end;

class function TGenFunc.GetField(S: string; FieldIndex: Integer; Delimiter: Char): string;
var
  DelimiterPos: Integer;
  loopCount: Integer;
  sRecord, sField: string;
begin
  loopCount := 1;
  sRecord := S;
  while loopCount <= FieldIndex do
  begin
    DelimiterPos := Pos(Delimiter, sRecord);
    if DelimiterPos <> 0 then
    begin
      sField := Copy(sRecord, 1, DelimiterPos - 1);
      Delete(sRecord, 1, DelimiterPos);
    end
    else
      sField := sRecord;
    loopCount := loopCount + 1;
  end;
  Result := sField;
end;

class function TGenFunc.GetIniName: string;
begin
  Result := TGenFunc.GetBaseFolder + 'Config.ini';
end;

class procedure TGenFunc.QuickSort(var A: array of Integer; iLo, iHi: Integer);
var
  Lo, Hi, Pivot, T: Integer;
begin
  Lo := iLo;
  Hi := iHi;
  Pivot := A[(Lo + Hi) div 2];

  repeat
    while A[Lo] < Pivot do
      Inc(Lo);
    while A[Hi] > Pivot do
      Dec(Hi);
    if Lo <= Hi then
    begin
      T := A[Lo];
      A[Lo] := A[Hi];
      A[Hi] := T;
      Inc(Lo);
      Dec(Hi);
    end;
  until Lo > Hi;

  if Hi > iLo then
    QuickSort(A, iLo, Hi);
  if Lo < iHi then
    QuickSort(A, Lo, iHi);
end;

end.

