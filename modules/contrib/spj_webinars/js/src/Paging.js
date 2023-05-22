
function Paging(props){

    let tot = props.total;
    let pages= Math.ceil(tot/20);
    let pageArr=[];
    for(let i=0; i<pages; i++){
        pageArr.push(i+1)
    }
    return(
        <div className="btn-group">
        
            {pageArr.map(index=>{

                return(
                <button 
                onClick={props.handlePage}
                data-page={index}
                className={`btn btn-outline-secondary ${(props.page+1==index? "active": "")}`} key={index}>
                    {index}
                </button>
                )
            })}
        </div>
    )
}

export default Paging;