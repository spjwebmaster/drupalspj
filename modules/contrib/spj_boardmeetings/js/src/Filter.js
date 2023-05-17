
function Filter(props){

    return(
    <div className="board-filter">
        <select className="form-control" value={props.type} onChange={props.handleType}>
            <option value="all">All Types</option>
            <option value="1115">SPJ Board Meeting</option>
            <option value="1117">Foundation Board Meeting</option>
        </select>

    </div>
    )
}

export default Filter;        