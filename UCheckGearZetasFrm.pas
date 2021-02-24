unit UCheckGearZetasFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.ScrollBox,
  FMX.Memo, FMX.StdCtrls, FMX.Edit, FMX.Controls.Presentation,
  UInterfaces, uUnit;

type
  TCheckGearZetasFrm = class(TForm, IChildren)
    pChkGuild: TPanel;
    lChkGuild: TLabel;
    eChkGuild: TEdit;
    bToClbd: TButton;
    mData: TMemo;
    lSteps: TLabel;
    procedure bToClbdClick(Sender: TObject);
  private
    FChar: TUnitList;

    procedure CheckZetasGear;
  public
    function SetCaption: string; virtual;
    function ShowOkButton: Boolean; virtual;
    function ShowBackButton: Boolean; virtual;
    function AcceptForm: Boolean; virtual;
    procedure AfterShow; virtual;
  end;

var
  CheckGearZetasFrm: TCheckGearZetasFrm;

implementation

uses
  System.IOUtils, System.DateUtils,
  uRESTMdl, uGenFunc, uGuild, uCharacter;

{$R *.fmx}

{ TCheckGearZetasFrm }

function TCheckGearZetasFrm.AcceptForm: Boolean;
var
  Intf: IMainMenu;
begin
  Result := False;

  mData.Lines.Clear;

  if eChkGuild.Text = '' then
    Exit;

  if Pos('http', eChkGuild.Text) <> 0 then
    eChkGuild.Text := TGenFunc.GetField(eChkGuild.Text, 5, '/');

  // mostrem animació
  if Supports(Owner, IMainMenu, Intf)  then
    Intf.ShowAni(True);

  lSteps.Text := 'Loading Data....';

  TThread.CreateAnonymousThread(procedure
  var
    Mdl: TRESTMdl;
    FileName: string;
  begin
    FileName := eChkGuild.Text + '_guild.json';

    // busquem dades de la Guild si no existeix o si l'arxiu té més de 24 hores
    if not TFile.Exists(FileName) or
       (TFile.Exists(FileName) and (IncHour(TFile.GetLastWriteTime(FileName), 24) < Now)) then
    begin
      Mdl := TRESTMdl.Create(nil);
      try
        Mdl.LoadData(tcGuild, eChkGuild.Text);
      finally
        FreeAndNil(Mdl);
      end;
    end;

    // Check de la Guild
    TThread.Synchronize(TThread.CurrentThread,
      procedure
      begin
        CheckZetasGear;
      end);

    // ocultem animació
    TThread.Synchronize(TThread.CurrentThread,
      procedure
      begin
        lSteps.Text := '';
        if Supports(Owner, IMainMenu, Intf) then
          Intf.ShowAni(False);
      end);
  end
  ).Start;
end;

procedure TCheckGearZetasFrm.AfterShow;
var
  L: TStringList;
begin
  lSteps.Text := '';

  // carreguem personatges
  if TFile.Exists(TGenFunc.GetBaseFolder + uCharacter.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(TGenFunc.GetBaseFolder + uCharacter.cFileName);
      FChar := TCharacters.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;
  end;
end;

procedure TCheckGearZetasFrm.bToClbdClick(Sender: TObject);
begin
  TGenFunc.CopyToClipboard(mData.Lines.Text);
end;

procedure TCheckGearZetasFrm.CheckZetasGear;
var
  Guild: TGuild;
  L: TStringList;
  i, j: Integer;
  TmpS: string;
  Idx: Integer;
  Str1, Str2: string;
begin
  if not TFile.Exists(eChkGuild.Text + '_guild.json') then
    Exit;

  L := TStringList.Create;
  try
    L.LoadFromFile(eChkGuild.Text + '_guild.json');
    Guild := TGuild.FromJsonString(L.Text);
  finally
    FreeAndNil(L);
  end;

  TmpS := '';
  for i := 0 to Guild.Count do
    TmpS := TmpS + #9 + Guild.Players[i].Data.Name;
  mData.Lines.Add(TmpS);

  // loop pels personatges
  for i := 0 to FChar.Count do
  begin
    TmpS := FChar.Items[i].Name;

    // loop pels players
    for j := 0 to Guild.Count do
    begin
      Idx := Guild.Players[j].IndexOf(FChar.Items[i].Base_Id);
      if Idx = -1 then // no ho té
        TmpS := TmpS + #9 + 'g0|z0'
      else
      begin
        if Guild.Players[j].Units[Idx].Data.CountZ = -1 then
          Str1 := 'z0'
        else
          Str1 := 'z' + (Guild.Players[j].Units[Idx].Data.CountZ + 1).ToString;

        if Guild.Players[j].Units[Idx].Data.Gear_level = 13 then
        begin
          if Guild.Players[j].Units[Idx].Data.Relic_tier - 2 < 0 then
            Str2 := 'g13r0'
          else
            Str2 := 'g13r' + (Guild.Players[j].Units[Idx].Data.Relic_tier - 2).ToString;
        end
        else
          Str2 := 'g' + Guild.Players[j].Units[Idx].Data.Gear_level.ToString;

        TmpS := TmpS + #9 + Str2 + '|' + Str1;
      end;
    end;

    mData.Lines.Add(TmpS);
  end;
end;

function TCheckGearZetasFrm.SetCaption: string;
begin
  Result := '';
end;

function TCheckGearZetasFrm.ShowBackButton: Boolean;
begin
  Result := True;
end;

function TCheckGearZetasFrm.ShowOkButton: Boolean;
begin
  Result := True;
end;

end.
