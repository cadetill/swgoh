unit uInterfaces;

interface

uses
  FMX.Forms, FMX.Types;

type
  IChildren = interface
    ['{1E07F6BA-1273-442C-B1F9-7B10CC2AB63E}']
    function SetCaption: string;
    function AcceptForm: Boolean;
    function ShowBackButton: Boolean;
    function ShowOkButton: Boolean;
    procedure AfterShow;
  end;

  IMainMenu = interface
    ['{E1646D23-CB62-4E82-8A04-8E51AABAD439}']
    function ShowAni(Show: Boolean): Boolean;
    procedure ShowAcceptButton(State: Boolean);
    procedure CreateForm(ClassForm: TFmxObjectClass; DataObject: TObject);
  end;

implementation

end.
