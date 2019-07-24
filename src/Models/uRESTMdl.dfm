object RESTMdl: TRESTMdl
  OldCreateOrder = False
  Height = 187
  Width = 215
  object RESTClient1: TRESTClient
    Accept = 'application/json, text/plain; q=0.9, text/html;q=0.8,'
    AcceptCharset = 'UTF-8, *;q=0.8'
    BaseURL = 'https://swgoh.gg/api/characters'
    Params = <>
    RaiseExceptionOn500 = False
    Left = 87
    Top = 8
  end
  object RESTRequest1: TRESTRequest
    Client = RESTClient1
    Params = <>
    Response = RESTResponse1
    SynchronizedEvents = False
    Left = 87
    Top = 64
  end
  object RESTResponse1: TRESTResponse
    ContentType = 'application/json'
    Left = 87
    Top = 118
  end
end
