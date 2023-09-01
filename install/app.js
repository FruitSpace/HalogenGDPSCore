var page=0

const byId=(id)=>document.getElementById(id)

const propagatePage=(id, hide=false)=> {
    let step = byId(`step-${id}`)
    let content = byId(`content-${id}`)
    if (hide) {
        step.classList.remove("bg-blue-600")
        content.classList.remove("!flex")
    }else{
        step.classList.add("bg-blue-600")
        content.classList.add("!flex")
    }
}

const showError = (text, show=true) => {
    byId(`error-${page}`).innerText=text
    if (show)
        byId(`error-${page}`).classList.remove("!hidden")
    else
        byId(`error-${page}`).classList.add("!hidden")
}

const initDB = async ()=> {
    let host = byId('db-host').value
    host=(host===""?"localhost":host) //enforce unix socket usage
    let port = byId('db-port').value
    let user = byId('db-user').value
    let pass = byId('db-pass').value
    let dbname = byId('db-name').value

    let d = new FormData()
    d.set("host",host)
    d.set("port",port)
    d.set("uname",user)
    d.set("pass",pass)
    d.set("dbname",dbname)

    let resp = await fetch("apply.php?a=initdb", {method:'POST',body:d}).then(r=>r.json())
    if (resp.status!="ok") {
        showError(resp.data)
        return false
    }
}



const makeActions = async () => {
    switch (page) {
        default:
            return true
        case 1:
            return await initDB()
    }
}

const pageNext = async ()=>{
    let res = await makeActions()
    if(!res) return
    propagatePage(page,true)
    page++
    propagatePage(page)
}
const pagePrev = async ()=>{
    propagatePage(page,true)
    page--
    propagatePage(page)
}

window.onload = () => {
    propagatePage(page)
}
