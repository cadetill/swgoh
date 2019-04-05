unit UToSumFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs,
  FMX.Controls.Presentation, FMX.StdCtrls, FMX.Layouts, FMX.Edit, FMX.EditBox,
  FMX.NumberBox;

type
  TToSumFrm = class(TForm)
    GridPanelLayout1: TGridPanelLayout;
    lGearXII: TLabel;
    eGearXII: TNumberBox;
    lGearXI: TLabel;
    eGearXI: TNumberBox;
    lGearX: TLabel;
    eGearX: TNumberBox;
    lGearIX: TLabel;
    eGearIX: TNumberBox;
    eGearVIII: TNumberBox;
    lGearVIII: TLabel;
    lZetas: TLabel;
    eZetas: TNumberBox;
    e900_701: TNumberBox;
    l900_701: TLabel;
    e700_551: TNumberBox;
    l700_551: TLabel;
    e550_301: TNumberBox;
    l550_301: TLabel;
    e300_201: TNumberBox;
    l300_201: TLabel;
    e200_151: TNumberBox;
    l200_151: TLabel;
    e150_101: TNumberBox;
    l150_101: TLabel;
    l100: TLabel;
    e100: TNumberBox;
    procedure eGearXIIChange(Sender: TObject);
    procedure eGearXIChange(Sender: TObject);
    procedure eGearXChange(Sender: TObject);
    procedure eGearIXChange(Sender: TObject);
    procedure eGearVIIIChange(Sender: TObject);
    procedure eZetasChange(Sender: TObject);
    procedure e900_701Change(Sender: TObject);
    procedure e700_551Change(Sender: TObject);
    procedure e550_301Change(Sender: TObject);
    procedure e300_201Change(Sender: TObject);
    procedure e200_151Change(Sender: TObject);
    procedure e150_101Change(Sender: TObject);
    procedure e100Change(Sender: TObject);
  private
  public
    constructor Create(aOwner: TComponent); override;
  end;

var
  ToSumFrm: TToSumFrm;

implementation

uses
  uIniFiles, uGenFunc;

{$R *.fmx}

constructor TToSumFrm.Create(aOwner: TComponent);
begin
  inherited;

  TFileIni.SetFileIni(TGenFunc.GetIniName);
  eGearXII.Value := TFileIni.GetIntValue('TOSUM', 'GEARXII', 0);
  eGearXI.Value := TFileIni.GetIntValue('TOSUM', 'GEARXI', 0);
  eGearX.Value := TFileIni.GetIntValue('TOSUM', 'GEARX', 0);
  eGearIX.Value := TFileIni.GetIntValue('TOSUM', 'GEARIX', 0);
  eGearVIII.Value := TFileIni.GetIntValue('TOSUM', 'GEARVIII', 0);
  eZetas.Value := TFileIni.GetIntValue('TOSUM', 'ZETAS', 0);
  e900_701.Value := TFileIni.GetFloatValue('TOSUM', '900_701', 0);
  e700_551.Value := TFileIni.GetFloatValue('TOSUM', '700_551', 0);
  e550_301.Value := TFileIni.GetFloatValue('TOSUM', '550_301', 0);
  e300_201.Value := TFileIni.GetFloatValue('TOSUM', '300_201', 0);
  e200_151.Value := TFileIni.GetFloatValue('TOSUM', '200_151', 0);
  e150_101.Value := TFileIni.GetFloatValue('TOSUM', '150_101', 0);
  e100.Value := TFileIni.GetFloatValue('TOSUM', '100_0', 0);
end;

procedure TToSumFrm.eGearIXChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM', 'GEARIX', eGearIX.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eGearXChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM', 'GEARX', eGearX.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eGearXIChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM', 'GEARXI', eGearXI.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eGearXIIChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM', 'GEARXII', eGearXII.Value.ToString.ToInteger);
end;

procedure TToSumFrm.e100Change(Sender: TObject);
begin
  TFileIni.SetFloatValue('TOSUM', '100_0', e100.Value.ToString.ToExtended);
end;

procedure TToSumFrm.e150_101Change(Sender: TObject);
begin
  TFileIni.SetFloatValue('TOSUM', '150_101', e150_101.Value.ToString.ToExtended);
end;

procedure TToSumFrm.e550_301Change(Sender: TObject);
begin
  TFileIni.SetFloatValue('TOSUM', '550_301', e550_301.Value.ToString.ToExtended);
end;

procedure TToSumFrm.e900_701Change(Sender: TObject);
begin
  TFileIni.SetFloatValue('TOSUM', '900_701', e900_701.Value.ToString.ToExtended);
end;

procedure TToSumFrm.e300_201Change(Sender: TObject);
begin
  TFileIni.SetFloatValue('TOSUM', '300_201', e300_201.Value.ToString.ToExtended);
end;

procedure TToSumFrm.e200_151Change(Sender: TObject);
begin
  TFileIni.SetFloatValue('TOSUM', '200_151', e200_151.Value.ToString.ToExtended);
end;

procedure TToSumFrm.e700_551Change(Sender: TObject);
begin
  TFileIni.SetFloatValue('TOSUM', '700_551', e700_551.Value.ToString.ToExtended);
end;

procedure TToSumFrm.eZetasChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM', 'ZETAS', eZetas.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eGearVIIIChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM', 'GEARVIII', eGearVIII.Value.ToString.ToInteger);
end;

end.

