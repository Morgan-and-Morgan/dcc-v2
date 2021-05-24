const HTMLElementsToStrip = [
  'script%3E',
  'html%3E',
  'body%3E',
  'head%3E',
  'p%3E',
  'input%3E',
  'a%3E',
  'form%3E',
  'textarea%3E',
  'button%3E',
  'ul%3E',
  'ol%3E',
  'li%3E',
  'main%3E',
  'footer%3E',
  'style%3E',
  'em%3E',
  'strong%3E',
  'meta%3E',
  'link%3E',
  'div%3E',
  '%3Cscript',
  '%3Chtml',
  '%3Cbody',
  '%3Chead',
  '%3Cp',
  '%3Cinput',
  '%3Ca',
  '%3Cform',
  '%3Ctextarea',
  '%3Cbutton',
  '%3Cul',
  '%3Col',
  '%3Cli',
  '%3Cmain',
  '%3Cfooter',
  '%3Cstyle',
  '%3Cem',
  '%3Cstrong',
  '%3Cmeta',
  '%3Clink',
  '%3Cdiv',
  '%3C%2Fscript',
  '%3C%2Fhtml',
  '%3C%2Fbody',
  '%3C%2Fhead',
  '%3C%2Fp',
  '%3C%2Finput',
  '%3C%2Fa',
  '%3C%2Fform',
  '%3C%2Ftextarea',
  '%3C%2Fbutton',
  '%3C%2Ful',
  '%3C%2Fol',
  '%3C%2Fli',
  '%3C%2Fmain',
  '%3C%2Ffooter',
  '%3C%2Fstyle',
  '%3C%2Fem',
  '%3C%2Fstrong',
  '%3C%2Fmeta',
  '%3C%2Flink',
  '%3C%2Fdiv',
  '%3Cimg',
  '%3C%2F',
  '%2F%3E',
  '%3C',
  '%3E'
]

function stripURLEncodedHTML(text, items=HTMLElementsToStrip) {
    let replaceString = text;
    for (let i = 0; i < items.length; i++) {
        // global replacement
        let pos = replaceString.indexOf(items[i]);
        while (pos > -1) {
            replaceString = replaceString.replace(items[i], "");
            pos = replaceString.indexOf(items[i]);
        }
    }
    return replaceString;
};

export default stripURLEncodedHTML;
