program swgoh;

uses
  System.StartUpCopy,
  FMX.Forms,
  UMainFrm in 'src\UMainFrm.pas' {MainFrm},
  uCharacter in 'src\Classes\uCharacter.pas',
  uUnit in 'src\Classes\uUnit.pas',
  uPlayer in 'src\Classes\uPlayer.pas',
  uAbilities in 'src\Classes\uAbilities.pas',
  uShips in 'src\Classes\uShips.pas',
  uGuild in 'src\Classes\uGuild.pas',
  UHomeFrm in 'src\UHomeFrm.pas' {HomeFrm},
  uRESTMdl in 'src\Models\uRESTMdl.pas' {RESTMdl: TDataModule},
  uBase in 'src\Classes\uBase.pas',
  UToSumFrm in 'src\UToSumFrm.pas' {ToSumFrm},
  uMessage in 'src\Classes\uMessage.pas',
  uToastUnit in 'src\Classes\uToastUnit.pas',
  uIniFiles in 'src\Classes\uIniFiles.pas',
  uGenFunc in 'src\Classes\uGenFunc.pas',
  UDefineTeamsFrm in 'src\UDefineTeamsFrm.pas' {DefineTeamsFrm},
  uTeams in 'src\Classes\uTeams.pas',
  UTeamFrm in 'src\UTeamFrm.pas' {TeamFrm},
  UInterfaces in 'src\Classes\UInterfaces.pas',
  UCheckPlayerFrm in 'src\UCheckPlayerFrm.pas' {CheckPlayerFrm},
  uMods in 'src\Classes\uMods.pas',
  UCheckGuildsFrm in 'src\UCheckGuildsFrm.pas' {CheckGuildsFrm},
  UBaseCheckFrm in 'src\Bases\UBaseCheckFrm.pas' {BaseCheckFrm},
  uGear in 'src\Classes\uGear.pas',
  UCheckTeamsFrm in 'src\UCheckTeamsFrm.pas' {CheckTeamsFrm},
  UCompPlayersFrm in 'src\UCompPlayersFrm.pas' {CompPlayersFrm};

{$R *.res}

begin
  Application.Initialize;
  Application.CreateForm(TMainFrm, MainFrm);
  Application.Run;
end.
