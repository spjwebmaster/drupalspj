import { useState, useEffect } from "react";
import 'bootstrap/dist/css/bootstrap.css';
import Filter from "./Filter";
import Paging from "./Paging";
import Webinar from "./Webinar";

function Webinars(){

    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true)
    const [includes, setIncludes] = useState([])
    const [webinar, setWebinar] = useState(null)
    const [webinarID, setWebinarID] = useState(null)

    

    const handleWebinarLink = (e, entity)=> {
        e.preventDefault();

        const id = e.target.getAttribute("data-id");
        //const entity = e.target.getAttribute("data-entity")
        setWebinarID(id);
        setWebinar(entity)
    }
    const handleBackLink = (e)=> {

        setWebinar(null);
        setWebinarID(null);
    }


    useEffect(() => {
        setLoading(true)
        let baseUrl = "https://drupal.spjnetwork.org/jsonapi/node/webinar?sort=-created&include=field_thumb,field_tag";
        
        let url = window.location.href;
        let webbyId = "";
        if(url.indexOf("webinarID=")>-1){
            webbyId = url.substring(url.indexOf("webinarID=")+10, url.length);
            setWebinarID(webbyId)
        }

        if(webinar==null||webbyId!=""){
        fetch(baseUrl)
        .then(resp=>resp.json())
        .then(data=>{

            if(webbyId!=""){
                let filtered=  data.data.filter(t=>t.id==webbyId)[0];

                setWebinar(filtered);
            }
            setData(data.data);
            
            setIncludes(data.included)
            setLoading(false);
        })
        } else {
            setLoading(false)
        }
    }, []);



    return (
        <div className="outer-container"> 

            
            <div className="contentArea">


                {(loading?<div className="loader">Loading</div>:"")}

                {(webinar!=null?
                <Webinar type="single" actionLink={handleBackLink} webinarID={webinarID} includes={includes} data={webinar} />:
                data && data.map(item=>{

               
                    const thumbId = item.relationships.field_thumb.data.id;
                    const thumb = includes.filter(t=>t.id == thumbId)[0];
                    const tagId = (item.relationships.field_tag.data[0]?item.relationships.field_tag.data[0].id: "");
                    const tagName = (tagId!=""?includes.filter(t=>t.id==tagId)[0].attributes.name: "");
                return(
                <div className="entry mb-2" key={item.id}>
                    <Webinar type="list" actionLink={handleWebinarLink} includes={includes} data={item} />
               
                    
                    
                </div>
                )
                }))}
            </div>
            
       
        </div>
    )
}

export default Webinars;