{"version":3,"sources":["dynamiccardsform.bundle.js"],"names":["this","BX","Landing","UI","exports","main_core","landing_ui_form_baseform","landing_env","landing_ui_field_sourcefield","landing_loc","landing_main","DynamicCardsForm","_BaseForm","babelHelpers","inherits","options","_this","classCallCheck","possibleConstructorReturn","getPrototypeOf","call","type","code","presets","sync","forms","id","concat","replace","Utils","random","onSourceChangeHandler","onSourceChange","dynamicParams","settingFieldsSelectors","sourceField","createSourceField","pagesField","createPagesField","addField","detailPageGroup","Card","DynamicFieldsGroup","items","createLinkField","addCard","createClass","key","value","_this2","sourceItems","getSourceItems","_sourceItems","slicedToArray","firstItem","source","filter","Type","isPlainObject","settings","sort","SourceField","selector","title","Loc","getMessage","onValueChange","field","fieldValue","getValue","getSources","find","item","setTimeout","isDetailPageAllowed","Dom","style","layout","Field","Pages","pagesCount","content","text","href","detailPage","Link","textOnly","disableCustomURL","disableBlocks","disallowType","allowedTypes","LinkURL","TYPE_PAGE","detailPageMode","fields","siteId","Env","getInstance","getOptions","site_id","landingId","Main","=TYPE","params","serialize","_this3","reduce","acc","includes","references","hasClass","stubs","src","alt","isReference","isString","sources","map","name","url","sortItem","isArray","some","reference","BaseForm","Form"],"mappings":"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","file":"dynamiccardsform.bundle.map.js"}