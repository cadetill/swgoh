unit uBase;

interface

type
  TBase = class
  private
  public
    procedure SaveToFile(FileName: string); virtual;
    procedure Compare(FileName: string); virtual;
    function ToJsonString: string;
  end;

  TBaseClass = class of TBase;

implementation

uses
  Rest.Json, REST.Json.Types, System.Classes, System.SysUtils;

{ TBase }

procedure TBase.Compare(FileName: string);
begin

end;

procedure TBase.SaveToFile(FileName: string);
var
  L: TStringList;
begin
  L := TStringList.Create;
  try
    L.Text := ToJsonString;
    L.SaveToFile(FileName);
  finally
    FreeAndNil(L);
  end;
end;

function TBase.ToJsonString: string;
begin
  Result := TJson.ObjectToJsonString(Self);
end;

end.
