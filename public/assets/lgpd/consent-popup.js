if(typeof Vue !== 'undefined')
{
    Vue.component('lgpd-consent-popup', {

        data: function ()
        {
            return {
                consented: false
            }
        },

        props: ['href'],

        methods:
        {
            consent: function()
            {
                localStorage.setItem('lgpd-consent', new Date);
            }
        },

        mounted: function ()
        {
            let consent = localStorage.getItem('lgpd-consent');
            this.consented = (consent) ? true : false;
        },

        template: `<div class="lgpd-consent-popup" v-show="consented">
            <p>Usamos cookies e tecnologias semelhantes de acordo com nossa
                    <a :href="href" target="_blank">Política de Privacidade</a>
                    e ao continuar a navegar em nosso conteúdo, você concorda com essas condições.</p>
            <button class="lgpd-consent-popup-button" @click="consent">OK</button>
        </div>`
    })
}