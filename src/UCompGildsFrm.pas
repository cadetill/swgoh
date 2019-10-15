unit UCompGildsFrm;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.ScrollBox,
  FMX.Memo, FMX.Objects, FMX.ListBox, FMX.Layouts, FMX.StdCtrls, FMX.Edit,
  FMX.Controls.Presentation,
  uInterfaces, uCompGuilds, uUnit;

type
  TCompGildsFrm = class(TForm, IChildren)
    pGuild1: TPanel;
    lGuild1: TLabel;
    eGuild1: TEdit;
    pGuild2: TPanel;
    lGuild2: TLabel;
    eGuild2: TEdit;
    pUnits: TPanel;
    cbUnits: TComboBox;
    bAddUnit: TButton;
    lbUnits: TListBox;
    ListBoxItem3: TListBoxItem;
    Line1: TLine;
    bToClbd: TButton;
    mData: TMemo;
    procedure bAddUnitClick(Sender: TObject);
    procedure lbUnitsDragChange(SourceItem, DestItem: TListBoxItem;
      var Allow: Boolean);
    procedure bToClbdClick(Sender: TObject);
  private
    FCGuilds: TCompGuilds;
    FChar: TUnitList;

    procedure LoadUnitsFromFile;
    procedure AddItem(Name, BaseId: string);
    procedure OnClickButton(Sender: TObject);
    procedure CompareGuilds(HtmlG1, HtmlG2: string);
  public
    function SetCaption: string;
    function ShowOkButton: Boolean;
    function ShowBackButton: Boolean;
    function AcceptForm: Boolean;
    procedure AfterShow;
  end;

var
  CompGildsFrm: TCompGildsFrm;

implementation

uses
  System.IOUtils,
  uGenFunc, uCharacter, uMessage, uRESTMdl, uGuild, uGuildInfo;

{$R *.fmx}

{ TCompGildsFrm }

function TCompGildsFrm.AcceptForm: Boolean;
var
  Intf: IMainMenu;
begin
  Result := False;

  if (eGuild1.Text = '') or (eGuild2.Text = '') then
    Exit;

  if Supports(Owner, IMainMenu, Intf)  then
    Intf.ShowAni(True);
  mData.Lines.Clear;

  TThread.CreateAnonymousThread(procedure
  var
    Mdl: TRESTMdl;
    HTMLG1: string;
    HTMLG2: string;
  begin
    Mdl := TRESTMdl.Create(nil);
    try
      Mdl.LoadData(tcGuild, TGenFunc.GetField(eGuild1.Text, 5, '/'));
      Mdl.LoadData(tcGuild, TGenFunc.GetField(eGuild2.Text, 5, '/'));
      HTMLG1 := Mdl.LoadData(tcURL, eGuild1.Text);
      HTMLG2 := Mdl.LoadData(tcURL, eGuild2.Text);

      TThread.Synchronize(TThread.CurrentThread,
        procedure
        begin
          CompareGuilds(HTMLG1, HTMLG2);
        end);
    finally
      FreeAndNil(Mdl);

      TThread.Synchronize(TThread.CurrentThread,
        procedure
        begin
          if Supports(Owner, IMainMenu, Intf)  then
            Intf.ShowAni(False);
        end);
    end;
  end
  ).Start;
end;

procedure TCompGildsFrm.AddItem(Name, BaseId: string);
var
  lbItem: TListBoxItem;
  Button: TButton;
begin
  lbItem := TListBoxItem.Create(lbUnits);
  lbItem.Text := Name;
  lbItem.TagString := BaseId;

  Button := TButton.Create(lbItem);
  Button.Align := TAlignLayout.Right;
  Button.Width := 40;
  Button.StyleLookup := 'trashtoolbutton';
  Button.Parent := lbItem;
  Button.OnClick := OnClickButton;

  lbUnits.AddObject(lbItem);

  FCGuilds.AddUnit(lbItem.TagString, lbItem.Text);

  cbUnits.ItemIndex := -1;
end;

procedure TCompGildsFrm.AfterShow;
begin
  LoadUnitsFromFile;
end;

procedure TCompGildsFrm.bAddUnitClick(Sender: TObject);
begin
  if cbUnits.ItemIndex = -1 then
    Exit;
  if lbUnits.Items.IndexOf(cbUnits.Items[cbUnits.ItemIndex]) <> -1 then
    Exit;

  AddItem(TUnit(cbUnits.Items.Objects[cbUnits.ItemIndex]).Name,
          TUnit(cbUnits.Items.Objects[cbUnits.ItemIndex]).Base_Id);
  FCGuilds.SaveToFile(TGenFunc.GetBaseFolder + uCompGuilds.cFileName);
end;

procedure TCompGildsFrm.bToClbdClick(Sender: TObject);
begin
  TGenFunc.CopyToClipboard(mData.Lines.Text)
end;

procedure TCompGildsFrm.CompareGuilds(HtmlG1, HtmlG2: string);
var
  i: Integer;
  L: TStringList;
  Guild1: TGuild;
  Guild2: TGuild;
  InfoG1: TGuildInfo;
  InfoG2: TGuildInfo;
  Idx1: Integer;
  Idx2: Integer;
  TmpVal1: Integer;
  TmpVal2: Integer;
begin
  if not TFile.Exists(TGenFunc.GetField(eGuild1.Text, 5, '/') + '_guild.json') or
     not TFile.Exists(TGenFunc.GetField(eGuild2.Text, 5, '/') + '_guild.json') then
    Exit;

  L := TStringList.Create;
  try
    L.LoadFromFile(TGenFunc.GetField(eGuild1.Text, 5, '/') + '_guild.json');
    Guild1 := TGuild.FromJsonString(L.Text);
    L.Text := '';
    L.LoadFromFile(TGenFunc.GetField(eGuild2.Text, 5, '/') + '_guild.json');
    Guild2 := TGuild.FromJsonString(L.Text);
  finally
    FreeAndNil(L);
  end;

  InfoG1 := nil;
  InfoG2 := nil;
  try
    InfoG1 := TGuildInfo.Create;
    InfoG2 := TGuildInfo.Create;

    InfoG1.Name := Guild1.Data.Name;
    InfoG2.Name := Guild2.Data.Name;
    InfoG1.GalacticPower := Guild1.Data.Galactic_power;
    InfoG2.GalacticPower := Guild2.Data.Galactic_power;

    InfoG1.GetAvgRank(HtmlG1);
    InfoG2.GetAvgRank(HtmlG2);

    InfoG1.GetInfoPlayers(Guild1, FCGuilds);
    InfoG2.GetInfoPlayers(Guild2, FCGuilds);

    mData.Lines.Add(''#9''#9 + InfoG1.Name + #9 + InfoG2.Name + #9 + 'Difference');
    mData.Lines.Add('Total PG'#9#9 + FormatFloat('#,##0', InfoG1.GalacticPower) + #9 + FormatFloat('#,##0', InfoG2.GalacticPower) + #9 + FormatFloat('#,##0', InfoG1.GalacticPower - InfoG2.GalacticPower));
    mData.Lines.Add('Avg Arena Rank'#9#9 + FormatFloat('#,##0', InfoG1.AvgArenaRank) + #9 + FormatFloat('#,##0', InfoG2.AvgArenaRank) + #9 + FormatFloat('#,##0', InfoG2.AvgArenaRank - InfoG1.AvgArenaRank));
    mData.Lines.Add('Avg Fleet Rank'#9#9 + FormatFloat('#,##0', InfoG1.AvgFeeltRank) + #9 + FormatFloat('#,##0', InfoG2.AvgFeeltRank) + #9 + FormatFloat('#,##0', InfoG2.AvgFeeltRank - InfoG1.AvgFeeltRank));
    mData.Lines.Add('Number of G13'#9#9 + InfoG1.Gear13.ToString + #9 + InfoG2.Gear13.ToString + #9 + (InfoG1.Gear13 - InfoG2.Gear13).ToString);
    mData.Lines.Add('Number of G12'#9#9 + InfoG1.Gear12.ToString + #9 + InfoG2.Gear12.ToString + #9 + (InfoG1.Gear12 - InfoG2.Gear12).ToString);
    mData.Lines.Add('Number of G11'#9#9 + InfoG1.Gear11.ToString + #9 + InfoG2.Gear11.ToString + #9 + (InfoG1.Gear11 - InfoG2.Gear11).ToString);
    mData.Lines.Add('Number of Zetas'#9#9 + InfoG1.Zetas.ToString + #9 + InfoG2.Zetas.ToString + #9 + (InfoG1.Zetas - InfoG2.Zetas).ToString);
    mData.Lines.Add('Mods 6*'#9#9 + InfoG1.Mods6.ToString + #9 + InfoG2.Mods6.ToString + #9 + (InfoG1.Mods6 - InfoG2.Mods6).ToString);
    mData.Lines.Add('Mods 25+*'#9#9 + InfoG1.Mods25.ToString + #9 + InfoG2.Mods25.ToString + #9 + (InfoG1.Mods25 - InfoG2.Mods25).ToString);
    mData.Lines.Add('Mods 20+*'#9#9 + InfoG1.Mods20.ToString + #9 + InfoG2.Mods20.ToString + #9 + (InfoG1.Mods20 - InfoG2.Mods20).ToString);
    mData.Lines.Add('Mods 15+*'#9#9 + InfoG1.Mods15.ToString + #9 + InfoG2.Mods15.ToString + #9 + (InfoG1.Mods15 - InfoG2.Mods15).ToString);
    mData.Lines.Add('Mods 10+'#9#9 + InfoG1.Mods10.ToString + #9 + InfoG2.Mods10.ToString + #9 + (InfoG1.Mods10 - InfoG2.Mods10).ToString);
    for i := 0 to FCGuilds.Count do
    begin
      Idx1 := InfoG1.IndexOf(FCGuilds.Items[i].Base_Id);
      Idx2 := InfoG2.IndexOf(FCGuilds.Items[i].Base_Id);
      if (Idx1 = -1) and (Idx2 = -1)then
        Continue;

      TmpVal1 := 0;
      if Idx1 <> -1 then
        TmpVal1 := InfoG1.Toons[Idx1].Gear13;
      TmpVal2 := 0;
      if Idx2 <> -1 then
        TmpVal2 := InfoG2.Toons[Idx2].Gear13;
      if (TmpVal1 <> 0) or (TmpVal2 <> 0) then
        mData.Lines.Add(FCGuilds.Items[i].Name + #9'Gear 13'#9 + TmpVal1.ToString + #9 + TmpVal2.ToString + #9 + (TmpVal1 - TmpVal2).ToString);

      TmpVal1 := 0;
      if Idx1 <> -1 then
        TmpVal1 := InfoG1.Toons[Idx1].Gear12;
      TmpVal2 := 0;
      if Idx2 <> -1 then
        TmpVal2 := InfoG2.Toons[Idx2].Gear12;
      if (TmpVal1 <> 0) or (TmpVal2 <> 0) then
        mData.Lines.Add(FCGuilds.Items[i].Name + #9'Gear 12'#9 + TmpVal1.ToString + #9 + TmpVal2.ToString + #9 + (TmpVal1 - TmpVal2).ToString);
    end;
  finally
    FreeAndNil(InfoG1);
    FreeAndNil(InfoG2);
  end;
end;

procedure TCompGildsFrm.lbUnitsDragChange(SourceItem, DestItem: TListBoxItem;
  var Allow: Boolean);
begin
  Allow := FCGuilds.Move(SourceItem.TagString, DestItem.TagString);
  FCGuilds.SaveToFile(TGenFunc.GetBaseFolder + uCompGuilds.cFileName);
end;

procedure TCompGildsFrm.LoadUnitsFromFile;
var
  L: TStringList;
  i: Integer;
begin
  cbUnits.Clear;
  lbUnits.Clear;

  // carreguem personatges desitjats
  if TFile.Exists(TGenFunc.GetBaseFolder + uCompGuilds.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(TGenFunc.GetBaseFolder + uCompGuilds.cFileName);
      FCGuilds := TCompGuilds.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;

    for i := 0 to FCGuilds.Count do
      AddItem(FCGuilds.Items[i].Name, FCGuilds.Items[i].Base_Id);
  end
  else
    FCGuilds := TCompGuilds.Create;

  // carreguem tots personatges
  if TFile.Exists(TGenFunc.GetBaseFolder + uCharacter.cFileName) then
  begin
    L := TStringList.Create;
    try
      L.LoadFromFile(TGenFunc.GetBaseFolder + uCharacter.cFileName);
      FChar := TCharacters.FromJsonString(L.Text);
    finally
      FreeAndNil(L);
    end;

    for i := 0 to FChar.Count do
      cbUnits.Items.AddObject(FChar.Items[i].Name, FChar.Items[i]);
  end;
end;

procedure TCompGildsFrm.OnClickButton(Sender: TObject);
begin
  if not (Sender is TButton) then Exit;

  TMessage.MsjSiNo('Are you sure to want to delete the Item "%s"?', [TListBoxItem(TButton(Sender).Owner).Text],
    procedure
    begin
      FCGuilds.DeleteUnit(TListBoxItem(TButton(Sender).Owner).TagString);
      FCGuilds.SaveToFile(TGenFunc.GetBaseFolder + uCompGuilds.cFileName);

      lbUnits.RemoveObject(TListBoxItem(TButton(Sender).Owner));
    end);
end;

function TCompGildsFrm.SetCaption: string;
begin
  Result := 'Compare Guilds';
end;

function TCompGildsFrm.ShowBackButton: Boolean;
begin
  Result := True;
end;

function TCompGildsFrm.ShowOkButton: Boolean;
begin
  Result := True;
end;

end.
