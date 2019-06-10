
        <!-- Modal -->
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/vue"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="<?=self::BASE_VACANCY_PATH?>js/main.js"></script>
        <script type="text/javascript">
            function App(){
                this.backendActionLinks = [
                    {class: "delete-user", action: "delete"},
                    {class: "delete-template", action: "delete"},
                    {class: "archive-vacancy", action: "archive"},
                    {class: "unarchive-vacancy", action: "unarchive"},
                ];
                this.modalCallLinks = [
                    {class: "choose-city", action: "modalcity"},
                    {class: "update-user-link", action: "update"},
                    {class: "update-template-link", action: "update"},
                    {class: "update-vacancy-link", action: "update"},
                    {class: "add-vacancy-btn", action: "add"},
                    {class: "add-template-btn", action: "add"},
                    {class: "add-user-btn", action: "add"},
                ];
                this.modalFormsLinks = [
                    {class: "add-user-form", action: "add"},
                    {class: "add-vacancy-form", action: "add"},
                    {class: "add-template-form", action: "add"},
                    {class: "choose-city-form", action: "add"},
                    {class: "update-vacancy-form", action: "update"},
                    {class: "update-user-form", action: "update"},
                    {class: "update-template-form", action: "update"},
                ];

                this.init = function()
                {
                    for(var i = 0; i < this.backendActionLinks.length; i++){
                        this.ajaxBackendAction(this.backendActionLinks[i].class, this.backendActionLinks[i].action);
                    }
                    for(var i = 0; i < this.modalCallLinks.length; i++){
                        this.ajaxModalCall(this.modalCallLinks[i].class, this.modalCallLinks[i].action);
                    }
                    for(var i = 0; i < this.modalFormsLinks.length; i++){
                        this.ajaxModalFormSend(this.modalFormsLinks[i].class, this.modalFormsLinks[i].action);
                    }
                    this.templatePaste();
                    $('.login-name').on('click', function(){
                        $(this).parent().find('ul').slideToggle();
                    });

                    $('.manager-vacancies').on('click', function(){
                        $(this).parent().find('.vacancies-wrap').slideToggle();
                    });
                }

                this.sendAjax = function(paramsObj, successFunc, url = false)
                {
                    if(!url){
                        url = window.location.pathname;
                    }
                    $.ajax({
                        type: "POST",
                        url: url,
                        data:paramsObj,
                        success: successFunc,
                    });
                };

                this.ajaxModalFormSend = function(className, action = "add")
                {
                    var formClass = "." + className,
                        thisFunc  = this;
                    $(document).on('submit', formClass, function(e){
                        e.preventDefault();
                        var url      = $(this).attr('action'),
                            params   = {
                                serializeData : $(this).serialize(),
                                form          : action,
                                elemID        : $(this).data('id') ? $(this).data('id') : false,
                            };
                        thisFunc.sendAjax(params, function(data){
                            if(data == "SUCCESS"){
                                location.reload();
                            }else{
                                $('.modal-body').html(data);
                            }
                        }, url);
                    });
                }

                this.ajaxBackendAction = function(className, action)
                {
                    var deleteLinks = document.getElementsByClassName(className),
                        thisFunc = this;
                    for(var i = 0; i < deleteLinks.length; i++){
                        deleteLinks[i].addEventListener('click', function(e){
                            e.preventDefault();
                            e.stopPropagation();
                            var elemID = e.target.dataset.id,
                                params = {elemID: elemID, actionType: action};
                            console.log(params)
                            thisFunc.sendAjax(params, function(data){location.reload();});
                        }, false);
                    }
                };

                this.ajaxModalCall = function(className, action)
                {
                    var links    = document.getElementsByClassName(className),
                        thisFunc = this;
                    for(var i = 0; i < links.length; i++){
                        links[i].addEventListener('click', function(e){
                            e.preventDefault();
                            e.stopPropagation();
                            var elemID = e.target.dataset.id ? e.target.dataset.id : false,
                                url    = e.target.dataset.path,
                                params = {
                                    actionType : className,
                                    form       : action,
                                    elemID     : elemID,
                                };
                            thisFunc.sendAjax(params, function(data){
                                $('.modal-body').html(data);
                                $("#modal").modal();
                            }, url);
                        }, false);
                    }
                };

                this.templatePaste = function()
                {
                    var thisFunc = this;
                    $(document).on('change', '.template-select', function(e){
                        var url    = $(this).data('path'),
                            params = {
                                elemID: $(this).val(),
                            };
                        thisFunc.sendAjax(params, function(data){
                            VACANCY_EDIT.SetEditorContent(data);
                        }, url);
                    });
                };
            }
            App = new App();
            App.init();
        </script>
    </body>
</html>
