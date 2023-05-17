import { useState, useEffect } from "react";
import 'bootstrap/dist/css/bootstrap.css';
import Filter from "./Filter";
import Paging from "./Paging";

function Boardmeetings(){

    const [type, setType] = useState("all");
    const [page, setPage] = useState(0);
    const [total, setTotal] = useState(0);
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true)
   
    const handleType = function(e){
        setPage(0);
        setType(e.target.value)
    }
    const handlePage = function(e){

        let page = e.target.getAttribute("data-page");
        setPage(page-1);
    }

    useEffect(() => {
        setLoading(true)
        let baseUrl = "http://drupal:8080/jsonapi/views/board_meeting_list/block_1/";
        baseUrl += "?page=" + page;
        if(type!="all"){
            baseUrl += "&views-filter[tid][0]=" + type;
        }
        fetch(baseUrl)
        .then(resp=>resp.json())
        .then(data=>{

            console.log(data.data);
            setTotal(data.meta.count);
            setData(data.data)
            setLoading(false)
        })
    }, [type, page]);



    return (
        <div className="outer-container"> 
            <Filter type={type} handleType={handleType} />
            <Paging total={total} page={page} handlePage={handlePage} />
            <hr />
            <div className="contentArea">
                {(loading?<div className="loader">Loading</div>:"")}
            {data.map(item=>{

                let typeNameId = item.relationships.field_meeting_type.data[0].meta.drupal_internal__target_id;
                let typeName = "";
                if(typeNameId=="1115"){
                    typeName = "SPJ Board Meeting";
                } else {
                    typeName = "Foundation Board Meeting";
                }
                var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                let meetingDate = new Date(item.attributes.field_meeting_date);
                let meetingDateShort = meetingDate.toLocaleDateString("en-US");
                let meetingDateFormat = meetingDate.toLocaleDateString("en-US", options)
                let meetingTime = (item.attributes.field_meeting_time_edt!=null?item.attributes.field_meeting_time_edt: "");
                
                let videoCode = "";
                if(item.attributes.field_replay_video){
                    let ytID = item.attributes.field_replay_video;
                    
                    if(ytID.indexOf("playlist")>-1){
                        //current: https://www.youtube.com/playlist?list=PLNitcNsxwFxKK-Zgv_q_-r6gE6zWh3Pz5
                        // target: https://www.youtube.com/embed/videoseries?list=PLNitcNsxwFxKK-Zgv_q_-r6gE6zWh3Pz5
                    } else if(ytID.indexOf(".be/")>-1){
                        ytID = ytID.replace("https://youtu.be/", "");
                        ytID = "https://www.youtube.com/embed/"+ytID;
                    } else if(ytID.indexOf("watch?v=")>-1){
                        ytID = ytID.substring(ytID.indexOf("watch?v=")+8, ytID.length);
                        ytID = "https://www.youtube.com/embed/"+ytID;
                    }
                    videoCode = `
                    <iframe width="560" height="315" src="${ytID}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>`
                }

                return(
                <div className="entry card mb-4" key={item.id}>
                    <h3 className="card-header">{typeName} - {meetingDateFormat} {(meetingTime!=""?<span className='badge text-white bg-info'>{meetingTime}</span>:"")}</h3>
                    <div className="card-body">
                        <div className="row">
                            <div className="col-sm-6">

                                <div className="row">

                                    <div className="col-sm-4">
                                        <strong>Meeting Link:</strong>
                                    </div>
                                    <div className="col-sm-8">
                                    {(item.attributes.field_meeting_link!==null? (<><a href={item.attributes.field_meeting_link}>Zoom link</a><br /></>): "N/A")}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-sm-4">
                                        <strong><label>Meeting Materials:</label></strong>
                                    </div>
                                    <div className="col-sm-8">
                                {(item.attributes.field_meeting_materials?<><a href={item.attributes.field_meeting_materials.uri}>{item.attributes.field_meeting_materials.title}</a></>: "N/A")}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-sm-4">
                                        <strong><label>Meeting Minutes:</label></strong>
                                    </div>
                                    <div className="col-sm-8">
                                    {(item.attributes.field_meeting_minutes?<><a href={item.attributes.field_meeting_minutes.uri}>{item.attributes.field_meeting_minutes.title}</a></>: "N/A")}
                                    </div>
                                </div>
                            </div>
                            <div className="col-sm-6">
                                <div className="row">
                                    <div className="col-sm-6">
                                        {((item.attributes.field_replay_video!=""&&item.attributes.field_replay_video!=null)?<strong>Replay</strong>:"")}
                                        <br />
                                        <div 
                                            className="replay"
                                            dangerouslySetInnerHTML={{__html: videoCode}}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                )
            })}
        </div>
        </div>
    )
}

export default Boardmeetings;