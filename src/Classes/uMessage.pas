unit uMessage;

interface

uses
  FMX.Dialogs, System.Classes, System.SysUtils,
  uToastUnit;

type
  TMessage = class
  public
    class procedure Show(const Msj: string);
    class procedure MsjErr(const Msj: string; const Args: array of const);
    class procedure MsjAttn(const Msj: string; const Args: array of const);
    class procedure MsjInfo(const Msj: string; const Args: array of const);
    class procedure MsjSiNo(const Msj: string; const Args: array of const; YesProc: TProc = nil; NoProc: TProc = nil);
  end;

implementation

uses
  FMX.DialogService, System.UITypes;

{ TMessage }

class procedure TMessage.MsjAttn(const Msj: string; const Args: array of const);
begin
  TDialogService.MessageDialog(Format(Msj, Args), TMsgDlgType.mtWarning, [TMsgDlgBtn.mbOk], TMsgDlgBtn.mbOk, 0, nil);
end;

class procedure TMessage.MsjErr(const Msj: string; const Args: array of const);
begin
  TDialogService.MessageDialog(Format(Msj, Args), TMsgDlgType.mtError, [TMsgDlgBtn.mbOk], TMsgDlgBtn.mbOk, 0, nil);
end;

class procedure TMessage.MsjInfo(const Msj: string; const Args: array of const);
begin
  TDialogService.MessageDialog(Format(Msj, Args), TMsgDlgType.mtInformation, [TMsgDlgBtn.mbOk], TMsgDlgBtn.mbOk, 0, nil);
end;

class procedure TMessage.MsjSiNo(const Msj: string;
  const Args: array of const; YesProc: TProc; NoProc: TProc);
begin
  TDialogService.MessageDialog(Format(Msj, Args), System.UITypes.TMsgDlgType.mtConfirmation,
      [System.UITypes.TMsgDlgBtn.mbYes, System.UITypes.TMsgDlgBtn.mbNo],
      System.UITypes.TMsgDlgBtn.mbYes, 0,
      procedure(const AResult: TModalResult)
      begin
        case AResult of
          mrYES: if Assigned(YesProc) then YesProc;
          mrNo: if Assigned(NoProc) then NoProc;
        end;
      end);
end;

class procedure TMessage.Show(const Msj: string);
begin
  TThread.Synchronize(TThread.CurrentThread,
    procedure
    begin
      {$IFDEF ANDROID}
      uToastUnit.Toast(Msj, TToastLength.LongToast);
      {$ELSE}
      ShowMessage(Msj);
      {$ENDIF}
    end);
end;

end.
