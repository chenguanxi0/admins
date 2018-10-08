<style>
    #warning{
        position:fixed;
        _position:absolute;
        width:100%;
        height:100%;
        left:0;
        top:0;
        background:#000;
        opacity:0.5;
        -moz-opacity:0.5;
        filter:alpha(opacity=50);
        z-index:9999;
        display: none;
    }
    #pop{
        width: 400px;
        /*height: 300px;*/
        background: #fff;
        position: fixed;
        left: 50%;
        top: 60%;
        margin-left: -200px;
        margin-top: -150px;
        z-index: 999;
        border-radius: 5px;

    }
    #pop h3.title{
        text-align: center;
        font-size: 22px;
        color:red;
    }
</style>
<div id="warning">
    <div id="pop">
        <h3 class="title text-danger">
            请稍等...
        </h3>
    </div>
</div>