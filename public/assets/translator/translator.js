const Translator = 
{
    write(text, scope, vars)
    {
        scope = scope ?? '_'
        text = TRANSLATOR && TRANSLATOR[scope] && TRANSLATOR[scope][text] 
            ? TRANSLATOR[scope][text]
            : text;

        if(vars)
        {
            vars = typeof vars == "object" ? vars : [vars]
            for(let v of vars)
                text = text.replace(/%s/i, v);
        }
        
        return text
    }
}

Vue.directive('trans', 
{
    bind: function(element, binding)
    {
        let scope = element.getAttribute('scope') ?? '_';
            content = binding.value ?? element.innerText;

        let vars = element.getAttribute('vars');

        let text = Translator.write(content, scope)

        if(Object.keys(binding.modifiers).length == 0)
            return element.innerText = text;

        for(let prop in binding.modifiers)
            element.setAttribute(prop, text);
    },
});


Vue.filter('trans', function(value, scope, vars) {

    return Translator.write(value, scope, vars);

})