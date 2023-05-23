
function Webinar(props){

    const type=props.type;
    const actionLink = props.actionLink;
    const includes = props.includes;
    const item = props.data;
    
    console.log(item);

    const thumbId = item.relationships.field_thumb.data.id;
    const thumb = includes.filter(t=>t.id == thumbId)[0];
    const tagId = (item.relationships.field_tag.data[0]?item.relationships.field_tag.data[0].id: "");
    const tagName = (tagId!=""?includes.filter(t=>t.id==tagId)[0].attributes.name: "");
    
    let videoCode = "";
    let videoLink = "";
    let videoEmbed ="";
    if(type!="list"){
        if(item.attributes.field_youtube_embed!=null &&
            item.attributes.field_youtube_embed!=""){
            videoLink = item.attributes.field_youtube_embed;

            if(videoLink.indexOf("watch?v=")>-1){
                videoCode = videoLink.substring(videoLink.indexOf("watch?v=")+8, videoLink.length);
                videoCode = "https://www.youtube.com/embed/"+videoCode;
                videoEmbed = <iframe 
                    src={videoCode} 
                    frameborder="0"
                    width="600"
                    height="400"
                    allowfullscreen="allowfullscreen"
                    className="youtubeEmbed">

                </iframe>

            } else if(videoLink.indexOf(".be/")>-1){
                videoCode = videoLink.replace("https://youtu.be/", "");
                videoCode = "https://www.youtube.com/embed/"+videoCode;
                videoEmbed = <iframe 
                    src={videoCode} 
                    frameborder="0"
                    width="600"
                    height="400"
                    allowfullscreen="allowfullscreen"
                    className="youtubeEmbed">

                </iframe>
            }
        } else if(item.attributes.field_video_url!=""){
            videoLink = item.attributes.field_video_url;
            videoCode = "";
            videoEmbed = <video controls with='100%' poster={thumb.attributes.uri.url}>
                <source src={videoLink} />
            </video>
        }
    }
    
    return(
        <div>
            {(type!="list"?<p><button className='btn' onClick={actionLink}>Back to list</button>
            <br />
            </p>:"")}

            <div className="card">
                <div className="card-header">

                    <h3>
                        {(type=="list"?
                        <a href="#" onClick={event=>{actionLink(event,item)}} data-id={item.id}>{item.attributes.title}</a>
                        :item.attributes.title)}
                    </h3>
                    {tagId}
                </div>
                <div className="card-body">
                {(tagId!=""?
                    <><span className="badge bg-info text-white">
                    {tagName}
                </span><br /></>:"")}


                    <div className="row">
                        <div className={(type=="list"?"col-sm-8":"col-sm-12")}>
                            <div dangerouslySetInnerHTML={{__html: item.attributes.body.value}}></div>
                        </div>

                        {(type=="list"?
                            <div className="col-sm-4">
                                <a href="#" onClick={event=>{actionLink(event,item)}}>
                                    <img className="d-block w-100" src={`https://drupal.spjnetwork.org${thumb.attributes.uri.url}`} alt="webinar preview" />
                                </a>
                                
                            </div>
                            : 
                            <div className="col-sm-12">
                                <hr />
                                    <a href={videoLink} target="_blank">{videoLink}</a>
                                    <br />
                                    {videoEmbed}
                                
                                <br />
                                <a href={`?webinarID=${props.webinarID}`} target="_blank" className="shareLink">Link to this webinar</a>
                            </div>
                            
                        )}
                    </div>
                </div>
                <br />
            
            </div>
        </div>
    )
}

export default Webinar;