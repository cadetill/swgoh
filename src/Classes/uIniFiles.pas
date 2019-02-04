unit uIniFiles;

interface

uses
  System.SysUtils, System.IniFiles, System.Classes,
  UMessage;

type
  TFileIni = record
  private
    class var
      FNameIni: string;

    class property NameIni: string read FNameIni write FNameIni;
  public
    class function GetIntValue(const Section, key: string; Default: Integer = -1): Integer; static;
    class function GetFloatValue(const Section, key: string; Default: Extended = -1): Extended; static;
    class function GetStrValue(const Section, key: string): String; static;
    class function GetBolValue(const Section, key: string): Boolean; static;
    class function GetTimeValue(const Section, key: string): TTime; static;
    class function GetDateValue(const Section, key: string): TDate; static;
    class function GetSection(const Section: string; List: TStrings): boolean; overload; static;
    class function GetSection(const Section: string; List, Key: TStrings): boolean; overload; static;

    class function SetIntValue(const Section, key: string; const Value: integer): Boolean; static;
    class function SetFloatValue(const Section, key: string; const Value: Extended): Boolean; static;
    class function SetStrValue(const Section, key, Value: string): Boolean; static;
    class function SetBolValue(const Section, key: string; const Value: boolean): Boolean; static;
    class function SetTimeValue(const Section, key: string; const Value: TDateTime): Boolean; static;
    class function SetDateValue(const Section, key: string; const Value: TDateTime): Boolean; static;
    class function SetSection(const Section: string; Values: TStrings): boolean; static;

    class function DeleteSection(const Section: string): Boolean; static;

    class procedure SetFileIni(const aName: string); static;
  end;

resourcestring
  errIniNotDef = 'Fichero Configuración no especificado';
  errExeNoExist = 'Fichero EXE pasado por parámetros es inválido';
  errIniNotFound = 'Fichero de configuración innexistente';

implementation

{ TFileIni }

class function TFileIni.DeleteSection(const Section: string): Boolean;
var
  Ini: TIniFile;
begin
  Result := False;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Ini.EraseSection(Section);
  finally
    FreeAndNil(Ini);
  end;

  Result := True;
end;

class function TFileIni.GetBolValue(const Section, key: string): Boolean;
var
  Ini: TIniFile;
begin
  Result := False;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Result := Ini.ReadBool(Section, key, false);
  finally
    FreeAndNil(Ini);
  end;
end;

class function TFileIni.GetDateValue(const Section, key: string): TDate;
var
  Ini: TIniFile;
begin
  Result := EncodeDate(1800, 1, 1);

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Result := Ini.ReadDate(Section, key, Date);
  finally
    FreeAndNil(Ini);
  end;
end;

class function TFileIni.GetFloatValue(const Section, key: string;
  Default: Extended): Extended;
var
  Ini: TIniFile;
begin
  Result := Default;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Result := Ini.ReadFloat(Section, key, Default);
  finally
    FreeAndNil(Ini);
  end;
end;

class function TFileIni.GetIntValue(const Section, key: string; Default: Integer): Integer;
var
  Ini: TIniFile;
begin
  Result := Default;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Result := Ini.ReadInteger(Section, key, Default);
  finally
    FreeAndNil(Ini);
  end;
end;

class function TFileIni.GetSection(const Section: string; List: TStrings): Boolean;
var
  Ini: TIniFile;
  i: integer;
begin
  Result := false;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  List.Clear;
  Ini := TIniFile.Create(NameIni);
  try
    Ini.ReadSection(Section, List);
    for i := 0 to List.Count - 1 do
      List[i] := Ini.ReadString(Section, List[i], '');
    Result := True;
  finally
    FreeAndNil(Ini);
  end;
end;

class function TFileIni.GetSection(const Section: string; List,
  Key: TStrings): Boolean;
var
  Ini: TIniFile;
  i: integer;
begin
  Result := false;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  List.Clear;
  Key.Clear;
  Ini := TIniFile.Create(NameIni);
  try
    Ini.ReadSection(Section, Key);
    for i := 0 to Key.Count - 1 do
      List.Insert(i, Ini.ReadString(Section, Key[i], ''));
    Result := true;
  finally
    FreeAndNil(Ini);
  end;
end;

class function TFileIni.GetStrValue(const Section, key: string): string;
var
  Ini: TIniFile;
begin
  Result := '';

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Result := Ini.ReadString(Section, key, '');
  finally
    FreeAndNil(Ini);
  end;
end;

class function TFileIni.GetTimeValue(const Section, key: string): TTime;
var
  Ini: TIniFile;
begin
  Result := EncodeTime(0, 0, 0, 0);

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Result := Ini.ReadTime(Section, key, Time);
  finally
    FreeAndNil(Ini);
  end;
end;

class function TFileIni.SetBolValue(const Section, key: string; const Value: boolean): Boolean;
var
  Ini: TIniFile;
begin
  Result := false;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Ini.WriteBool(Section, key, Value);
  finally
    FreeAndNil(Ini);
  end;

  Result := true;
end;

class function TFileIni.SetDateValue(const Section, key: string;
  const Value: TDateTime): Boolean;
var
  Ini: TIniFile;
begin
  Result := false;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Ini.WriteDate(Section, key, Value);
  finally
    FreeAndNil(Ini);
  end;

  Result := true;
end;

class procedure TFileIni.SetFileIni(const aName: string);
begin
  FNameIni := aName;
end;

class function TFileIni.SetFloatValue(const Section, key: string;
  const Value: Extended): Boolean;
var
  Ini: TIniFile;
begin
  Result := false;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Ini.WriteFloat(Section, key, Value);
  finally
    FreeAndNil(Ini);
  end;

  Result := true;
end;

class function TFileIni.SetIntValue(const Section, key: string; const Value: integer): Boolean;
var
  Ini: TIniFile;
begin
  Result := false;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Ini.WriteInteger(Section, key, Value);
  finally
    FreeAndNil(Ini);
  end;

  Result := true;
end;

class function TFileIni.SetSection(const Section: string;
  Values: TStrings): boolean;
var
  i: Integer;
begin
  Result := False;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  DeleteSection(Section);
  for i := 0 to Values.Count - 1 do
    SetStrValue(Section, IntToStr(i), Values[i]);

  Result := True;
end;

class function TFileIni.SetStrValue(const Section, key, Value: string): Boolean;
var
  Ini: TIniFile;
begin
  Result := False;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Ini.WriteString(Section, key, Value);
  finally
    FreeAndNil(Ini);
  end;

  Result := True;
end;

class function TFileIni.SetTimeValue(const Section, key: string; const Value: TDateTime): Boolean;
var
  Ini: TIniFile;
begin
  Result := False;

  if NameIni = '' then
  begin
    TMessage.MsjErr(errIniNotDef, []);
    Exit;
  end;

  Ini := TIniFile.Create(NameIni);
  try
    Ini.WriteTime(Section, key, Value);
  finally
    FreeAndNil(Ini);
  end;

  Result := True;
end;

end.
