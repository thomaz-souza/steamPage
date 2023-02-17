<?php
    $this->using('CMSController\\Edit');

    Dynamic\Dynamic::addTag($this);
    Storm\Storm::addTag($this);

    $lang = $_GET['lang'];
    $languageFiles = $this->languageLoadFile($lang);
    unset($languageFiles['_']);

    $culture = new Culture\CMS();

    $availableLanguages = $culture->getAvailableLanguages();

    $this->variables['pageTitle'] = $this->write('Edit translation', 'admin') . " - " . $this->getLanguage($lang)['name'];
?>

<!-- Container -->

<div id="main">


    <!-- Modal -->
    <div class="modal fade" id="reflectModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" su:write="Reflect scope" su:scope="admin"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
            <p su:write="Select the language from which you want to fetch text" su:scope="admin"></p>

            <div class="row" >
                <div class="col-lg-12 form-group">
                    <select class="form-control" data-bind="optionsText: function(i){ return  i.label + ' | ' + i.name },options: availableLanguages, value: reflectFrom"></select>
                </div>

                <div class="col-lg-12 row">
                    <div class="col-lg-6 form-group">
                        <label su:write="What do you want to bring?" su:scope="admin"></label>
                        <select class="form-control" data-bind="value: reflectWhat">                            
                            <option value="<?= Culture\CMS::TRANSLATION_BRING_NEW ?>" su:write="Only copy new text" su:scope="admin"></option>
                            <option value="<?= Culture\CMS::TRANSLATION_BRING_ENTIRELY ?>" su:write="Reflect entirely" su:scope="admin"></option>
                        </select>
                    </div>
                    <div class="col-lg-6 form-group">
                        <label su:write="How the translation must come?" su:scope="admin"></label>
                        <select class="form-control" data-bind="value: reflectHow">
                            <option value="<?= Culture\CMS::TRANSLATION_NEW_BLANK ?>" su:write="Keep this language translations and add new texts as blank" su:scope="admin"></option>
                            <option value="<?= Culture\CMS::TRANSLATION_NEW_FROM ?>" su:write="Keep this language translations and add new texts with selected language text" su:scope="admin"></option>
                            <option value="<?= Culture\CMS::TRANSLATION_NEW_ORIGINAL ?>" su:write="Keep this language translations and add new texts with original text" su:scope="admin"></option>
                            <option value="<?= Culture\CMS::TRANSLATION_ALL_FROM ?>"><su: write="Discard this language translations and use the selected language translations" scope="admin"></option>
                            <option value="<?= Culture\CMS::TRANSLATION_ALL_ORIGINAL ?>"><su: write="Discard this language translations and use original text as translation" scope="admin"></option>
                            <option value="<?= Culture\CMS::TRANSLATION_ALL_BLANK ?>" su:write="All blank" su:scope="admin"></option>
                        </select>
                    </div>
                </div>
            </div>            

            <p su:write="Nothing will be replaced until you save the scope" su:scope="admin"></p>


          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><su: write="Cancel" scope="admin"></button>
            <button type="button" class="btn btn-success" data-bind="click: reflect"><su: write="Reflect" scope="admin"></button>
          </div>
        </div>
      </div>
    </div>

    <div class="row" data-bind="hidden: scopes().length > 0">
        <div class="col-lg-12">
                <!-- Portlet -->
            <div class="card card-custom card-stretch gutter-b">                
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span  su:write="New language" su:scope="admin"></span>
                    </h3>
                </div>

                <div class="card-body pt-4">
                    <p su:write="This language has no language files. Do you want to copy language files from another language?" su:scope="admin"></p>
                        
                        <div class="row">
                            <div class="kt-section kt-section--first row form-group col-lg-6">
                                <select class="form-control" data-bind="optionsText: function(i){ return  i.label + ' | ' + i.name },options: availableLanguages, value: copyFrom"></select>
                            </div>
                            <div class="col-lg-6">
                                <button class="btn btn-primary" data-bind="click: doCopy"><su: write="Copy" scope="admin"></button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    

    <div class="row" data-bind="hidden: scopes().length == 0, foreach: scopes">

        <div class="col-lg-12">
            <!-- Portlet -->
            <div class="card card-custom card-stretch gutter-b">            

                    <div class="card-header align-items-center border-0 mt-4">
                        <h3 class="card-title align-items-start flex-column">
                            <span data-bind="text: title, hidden: nameable()"></span>
                            <input type="text" data-bind="value: title, hidden: !nameable()" class="form-control">
                        </h3>

                        <div class="card-toolbar">
                            <button class="btn btn-success" data-bind="click: openReflectModal, hidden: nameable()"><i class="fas fa-stamp"></i> <su: write="Reflect" scope="admin"/></button>
                        </div>
                    
                    </div>
                <div class="card-body pt-4">
                    <form class="form" id="form">
                        <div class="row" data-bind="foreach: texts">

                            <div class="col-lg-12 row text-line" data-bind="class: erased() ? 'erased' : edited() ? 'edited' : ''">

                                <div class="col-lg-5" data-bind="class: originalError() ? 'error' : ''">
                                    <span data-bind="text: original, hidden: editingOriginal"></span>
                                    <textarea class="form-control" data-bind="value: original, visible: editingOriginal"></textarea>
                                </div>
                                <div class="col-lg-5" data-bind="class: translationError() ? 'error' : ''">
                                    <textarea class="form-control" data-bind="value: translation, disable: erased"></textarea>
                                </div>
                                <div class="col-lg-2">
                                     <div class="list-item-btn" data-bind="click: erase"  data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Delete' scope='admin'>"><i class="fas fa-trash"></i></div><div class="list-item-btn" data-bind="click: backToOriginal" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Restore to original' scope='admin'>"><i class="fas fa-undo-alt"></i></div><div class="list-item-btn" data-bind="click: editOriginal" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Edit original text' scope='admin'>"><i class="fas fa-pen"></i></i></div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary" data-bind="click: addText"><i class="fas fa-plus-circle"></i> <su: write="Add" scope="admin"></button>
                    <button class="btn btn-primary" data-bind="click: saveScope"><su: write="Save" scope="admin"></button>
                </div>              
            </div>
        </div>
    </div>
    <div class="card card-custom card-stretch gutter-b">
        <button class="btn btn-primary" data-bind="click: newScope"><i class="fas fa-plus-circle"></i> <su: write="Add new scope" scope="admin"></button>
    </div>
</div>

<style type="text/css">
    textarea.form-control{
        padding: 0;
        border-width: 0 !important;
        height: auto;
        background-color: transparent;
    }

    .text-line{
        border-bottom: 1px dotted #E7E7E7;
        padding: 10px 0px;
        transition: 0.5s all ease;
    }
    .edited{
        background-color: #f0fbf0;
        border-color: #d7f4d7;
    }
    .erased{
        background-color: #F3F3F3;
        border-color: #E7E7E7;
        text-decoration: line-through;
        color: #CCC;
    }
    .error{
        background-color: #fce8e8;
        border-color: #f4a4a4;
    }
    .list-item-btn
    {
        display: none;
        text-align: center;
        padding: 5px;
        margin: 5px;
        color: #CCC;
        cursor: pointer;
        font-size: 11px;
        transition: 0.3s all ease;
    }
    .text-line:hover .list-item-btn{
        display: inline-block;
    }
    .erased .list-item-btn{
        visibility: hidden;
    }
    
    .list-item-btn:hover{
        color: #333;
    }
</style>

<script>

    var Text = function (original, translation)
    {
        let self = this;
        
        self.original = ko.observable(original ? original : "");
        self.editingOriginal = ko.observable(!original);
        self.editOriginal = function(){
            self.editingOriginal(true);
        };

        self.translation = ko.observable(translation ? translation : "");

        self.backToOriginal = function()
        {
            self.original(original ? original : "");
            self.translation(translation ? translation : "");
        }

        self.erased = ko.observable(false);

        self.erase = function ()
        {
            self.erased(true);
        }

        self.edited = ko.computed(function(){
            return self.translation() != translation || self.original() != original;
        });

        self.originalError = ko.computed(function(){
            return (self.original() == null || self.original() == "")
        })

        self.translationError = ko.computed(function(){
            return (self.translation() == null || self.translation() == "");
        }); 

        self.erased = ko.observable(false);
    }

    var Scope = function (title, content)
    {
        let self = this;
        self.title = ko.observable(title ? title : '');

        self.title.subscribe(function(e){ self.title(e.replace(/[^A-z0-9-_\.\s]+/gi,"").replace(/\s/g,"_").toLowerCase()); });

        self.texts = ko.observableArray();
        
        self.setContent = function (content)
        {
            let texts = [];
            ko.utils.objectMap(content, function(i, k){
                texts.push(new Text(k,i));
            });
            self.texts(texts);
            $('[data-toggle="kt-tooltip"]').tooltip();  
        }

        self.nameable = ko.observable(!title);

        self.setContent(content ? content : []);

        self.saveScope = function() 
        {
            if(self.title().replace(/\s/gi,"") == "")
                return alert('<su: write="You need to name this scope" scope="admin">');

            let availableText = {};

            let hasError = false;

            ko.utils.arrayMap(self.texts(), function(t){
                if(!t.translationError() && !t.originalError() && !t.erased())
                    availableText[t.original()] = t.translation();
                else if (t.translationError() || t.originalError())
                    hasError = true;
            })

            if(!hasError || confirm('<su: write="There are still pending translations. If you proceed, these translations will not be saved. Are you sure you want to save?" scope="admin">'))
            {
                blockLoading();

                let callback = function(response)
                {
                    unblockLoading();
                    if(response.status == 200)
                    {
                        self.nameable(false);
                        self.setContent(response.data);                 
                    }
                }

                Edit.call({
                    _call: "Culture/CMS::saveLanguage",
                    scope: self.title(),
                    language: window.lang,
                    text: availableText
                }, callback);
            }
        }

        self.openReflectModal = function ()
        {
            editor.reflectScope(self);
            $('#reflectModal').modal('show');
        }

        self.addText = function()
        {
            self.texts.push(new Text);
        }
    }

    var editorModel = function ()
    {
        let self = this;
        self.scopes = ko.observableArray();

        self.availableLanguages = ko.observableArray(<?= json_encode($availableLanguages) ?>);
        self.copyFrom = ko.observable();

        self.setData = function (data)
        {
            let scopes = [];
            ko.utils.objectMap(data, function(i, k){
                scopes.push(new Scope(k,i));
            })
            self.scopes(scopes);          
        }

        self.newScope = function ()
        {
            self.scopes.push(new Scope());
        }

        self.doCopy = function ()
        {
            blockLoading();

            let callback = function(response)
            {
                unblockLoading();
                if(response.status == 200)
                    self.setData(response.data);                
            }

            Edit.call({
                _call: "Culture/CMS::copyLanguage",
                language: window.lang,
                from: self.copyFrom().label
            }, callback);
        }

        self.reflectFrom = ko.observable();
        self.reflectScope = ko.observable();
        self.reflectWhat = ko.observable();
        self.reflectHow = ko.observable();

        self.reflect = function ()
        {
            blockLoading();
            $('#reflectModal').modal('hide');

            let callback = function(response)
            {
                unblockLoading();
                if(response.status == 200)
                    self.reflectScope().setContent(response.data);                
            }

            Edit.call({
                _call: "Culture/CMS::reflectScope",
                language: window.lang,
                from: self.reflectFrom().label,
                scope: self.reflectScope().title(),
                translation: self.reflectHow(),
                bring: self.reflectWhat()
            }, callback);
        }
    }

    var lang = '<?= $lang ?>';
    var editor = new editorModel();
    editor.setData(<?= json_encode($languageFiles) ?>);
    ko.applyBindings(editor, document.getElementById('main'));

</script>