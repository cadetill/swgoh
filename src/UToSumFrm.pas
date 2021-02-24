unit UToSumFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs,
  FMX.Controls.Presentation, FMX.StdCtrls, FMX.Layouts, FMX.Edit, FMX.EditBox,
  FMX.NumberBox, FMX.TabControl, FMX.Objects;

type
  TToSumFrm = class(TForm)
    sbPlayers: TScrollBox;
    pGearXII: TPanel;
    lGearXII: TLabel;
    eGearXII: TNumberBox;
    pGearXI: TPanel;
    lGearXI: TLabel;
    eGearXI: TNumberBox;
    pGearX: TPanel;
    lGearX: TLabel;
    eGearX: TNumberBox;
    pGearIX: TPanel;
    lGearIX: TLabel;
    eGearIX: TNumberBox;
    pGearVIII: TPanel;
    lGearVIII: TLabel;
    eGearVIII: TNumberBox;
    pZetas: TPanel;
    lZetas: TLabel;
    eZetas: TNumberBox;
    p900_701: TPanel;
    l900_701: TLabel;
    e900_701: TNumberBox;
    p700_551: TPanel;
    l700_551: TLabel;
    e700_551: TNumberBox;
    p550_301: TPanel;
    l550_301: TLabel;
    e550_301: TNumberBox;
    p300_201: TPanel;
    l300_201: TLabel;
    e300_201: TNumberBox;
    p200_151: TPanel;
    l200_151: TLabel;
    e200_151: TNumberBox;
    p150_101: TPanel;
    l150_101: TLabel;
    e150_101: TNumberBox;
    p100: TPanel;
    l100: TLabel;
    e100: TNumberBox;
    tcToSum: TTabControl;
    tiPlayers: TTabItem;
    tiTeams: TTabItem;
    sbTeams: TScrollBox;
    pTGear: TPanel;
    lTGear: TLabel;
    eTGearOk: TNumberBox;
    pTZetas: TPanel;
    lTZetas: TLabel;
    eTZetasOk: TNumberBox;
    pTSpeed: TPanel;
    lTSpeed: TLabel;
    eTSpeedOk: TNumberBox;
    pTTitle: TPanel;
    lTitle: TLabel;
    lTitleOK: TLabel;
    lTitleKO: TLabel;
    eTGearKo: TNumberBox;
    pTeams: TPanel;
    pPlayers: TPanel;
    lnTitle: TLine;
    eTZetasKo: TNumberBox;
    eTSpeedKo: TNumberBox;
    pTTenacity: TPanel;
    lTTenacity: TLabel;
    eTTenacityOk: TNumberBox;
    eTTenacityKo: TNumberBox;
    pTFDam: TPanel;
    lTFDam: TLabel;
    eTFDamOk: TNumberBox;
    eTFDamKo: TNumberBox;
    pTSDam: TPanel;
    lTSDam: TLabel;
    eTSDamOk: TNumberBox;
    eTSDamKo: TNumberBox;
    pTHealth: TPanel;
    lTHealth: TLabel;
    eTHealthOk: TNumberBox;
    eTHealthKo: TNumberBox;
    pPG: TPanel;
    lPG: TLabel;
    eTPGOk: TNumberBox;
    eTPGKo: TNumberBox;
    tiGear: TTabItem;
    pGear: TPanel;
    sbGear: TScrollBox;
    pMaxGear: TPanel;
    lMaxGear: TLabel;
    eMaxGear: TNumberBox;
    pGearXIII: TPanel;
    lGearXIII: TLabel;
    eGearXIII: TNumberBox;
    pTPotency: TPanel;
    lTPotency: TLabel;
    eTPotencyOk: TNumberBox;
    eTPotencyKo: TNumberBox;
    pTCriChance: TPanel;
    lTCriChance: TLabel;
    eTCriChanceOk: TNumberBox;
    eTCriChanceKo: TNumberBox;
    pTRelic: TPanel;
    lTRelic: TLabel;
    eTRelicOk: TNumberBox;
    eTRelicKo: TNumberBox;
    pTProtection: TPanel;
    lTProtection: TLabel;
    eTProtectionOk: TNumberBox;
    eTProtectionKo: TNumberBox;
    procedure eGearXIIChange(Sender: TObject);
    procedure eGearXIChange(Sender: TObject);
    procedure eGearIXChange(Sender: TObject);
    procedure eGearXChange(Sender: TObject);
    procedure eGearVIIIChange(Sender: TObject);
    procedure eZetasChange(Sender: TObject);
    procedure e900_701Change(Sender: TObject);
    procedure e700_551Change(Sender: TObject);
    procedure e550_301Change(Sender: TObject);
    procedure e300_201Change(Sender: TObject);
    procedure e200_151Change(Sender: TObject);
    procedure e150_101Change(Sender: TObject);
    procedure e100Change(Sender: TObject);
    procedure eTZetasOkChange(Sender: TObject);
    procedure eTSpeedOkChange(Sender: TObject);
    procedure eTGearOkChange(Sender: TObject);
    procedure eTGearKoChange(Sender: TObject);
    procedure eTSpeedKoChange(Sender: TObject);
    procedure eTHealthOkChange(Sender: TObject);
    procedure eTHealthKoChange(Sender: TObject);
    procedure eTTenacityOkChange(Sender: TObject);
    procedure eTTenacityKoChange(Sender: TObject);
    procedure eTFDamOkChange(Sender: TObject);
    procedure eTFDamKoChange(Sender: TObject);
    procedure eTSDamOkChange(Sender: TObject);
    procedure eTSDamKoChange(Sender: TObject);
    procedure eTZetasKoChange(Sender: TObject);
    procedure eTPGOkChange(Sender: TObject);
    procedure eTPGKoChange(Sender: TObject);
    procedure eMaxGearChange(Sender: TObject);
    procedure eGearXIIIChange(Sender: TObject);
    procedure eTPotencyKoChange(Sender: TObject);
    procedure eTPotencyOkChange(Sender: TObject);
    procedure eTCriChanceOkChange(Sender: TObject);
    procedure eTCriChanceKoChange(Sender: TObject);
    procedure eTRelicOkChange(Sender: TObject);
    procedure eTRelicKoChange(Sender: TObject);
    procedure eTProtectionOkChange(Sender: TObject);
    procedure eTProtectionKoChange(Sender: TObject);
  private
    procedure SetWidthTeamComponents;
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

  tcToSum.ActiveTab := tiPlayers;

  SetWidthTeamComponents;

  TFileIni.SetFileIni(TGenFunc.GetIniName);

  eGearXIII.Value := TFileIni.GetIntValue('TOSUM', 'GEARXIII', 0);
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

  eTPGOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'PGOK', 0);
  eTPGKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'PGKO', 0);
  eTGearOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'GEAROK', 0);
  eTGearKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'GEARKO', 0);
  eTSpeedOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'SPEEDOK', 0);
  eTSpeedKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'SPEEDKO', 0);
  eTHealthOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'HEALTHOK', 0);
  eTHealthKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'HEALTHKO', 0);
  eTProtectionOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'PROTECTIONOK', 0);
  eTProtectionKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'PROTECTIONKO', 0);
  eTTenacityOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'TENACITYOK', 0);
  eTTenacityKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'TENACITYKO', 0);
  eTPotencyOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'POTENCYOK', 0);
  eTPotencyKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'POTENCYKO', 0);
  eTFDamOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'FDAMAGEOK', 0);
  eTFDamKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'FDAMAGEKO', 0);
  eTSDamOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'SDAMAGEOK', 0);
  eTSDamKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'SDAMAGEKO', 0);
  eTZetasOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'ZETASOK', 0);
  eTZetasKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'ZETASKO', 0);
  eTCriChanceOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'CRITCHANCEOK', 0);
  eTCriChanceKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'CRITCHANCEKO', 0);
  eTRelicOk.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'RELICTIEROK', 0);
  eTRelicKo.Value := TFileIni.GetFloatValue('TOSUM_TEAMS', 'RELICTIERKO', 0);

  eMaxGear.Value := TFileIni.GetFloatValue('GEAR', 'MAXGEAR', 0);
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

procedure TToSumFrm.eGearXIIIChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM', 'GEARXIII', eGearXIII.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eMaxGearChange(Sender: TObject);
begin
  TFileIni.SetIntValue('GEAR', 'MAXGEAR', eMaxGear.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTProtectionKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'PROTECTIONKO', eTProtectionKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTProtectionOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'PROTECTIONOK', eTProtectionOk.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTPotencyKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'POTENCYKO', eTPotencyKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTPotencyOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'POTENCYOK', eTPotencyOk.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTRelicKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'RELICTIERKO', eTZetasKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTRelicOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'RELICTIEROK', eTZetasOk.Value.ToString.ToInteger);
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

procedure TToSumFrm.SetWidthTeamComponents;
var
  TmpI: Integer;
begin
  TmpI := (Trunc(pTGear.Width - lTitle.Width) div 2) - Trunc(lTitleKO.Margins.Left + pTTitle.Margins.Left + pTTitle.Margins.Right);

  lTitleOK.Width := TmpI;
  eTPGOk.Width := TmpI;
  eTGearOk.Width := TmpI;
  eTZetasOk.Width := TmpI;
  eTSpeedOk.Width := TmpI;
  eTTenacityOk.Width := TmpI;
  eTFDamOk.Width := TmpI;
  eTSDamOk.Width := TmpI;
  eTHealthOk.Width := TmpI;
end;

procedure TToSumFrm.eTCriChanceKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'CRITCHANCEKO', eTCriChanceKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTCriChanceOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'CRITCHANCEOK', eTCriChanceOk.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTFDamKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'FDAMAGEKO', eTFDamKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTFDamOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'FDAMAGEOK', eTFDamOk.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTGearKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'GEARKO', eTGearKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTGearOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'GEAROK', eTGearOk.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTHealthKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'HEALTHKO', eTHealthKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTHealthOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'HEALTHOK', eTHealthOk.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTPGKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'PGKO', eTPGKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTPGOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'PGOK', eTPGOk.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTSDamKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'SDAMAGEKO', eTSDamKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTSDamOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'SDAMAGEOK', eTSDamOk.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTSpeedKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'SPEEDKO', eTSpeedKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTSpeedOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'SPEEDOK', eTSpeedOk.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTTenacityKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'TENACITYKO', eTTenacityKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTTenacityOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'TENACITYOK', eTTenacityOk.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTZetasKoChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'ZETASKO', eTZetasKo.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eTZetasOkChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM_TEAMS', 'ZETASOK', eTZetasOk.Value.ToString.ToInteger);
end;

procedure TToSumFrm.eGearVIIIChange(Sender: TObject);
begin
  TFileIni.SetIntValue('TOSUM', 'GEARVIII', eGearVIII.Value.ToString.ToInteger);
end;

end.

