

function getListHTML(list) {
    var HTML = "";
    HTML += "<div data-listid='" + list.id + "' id='lists-" + list.id + "' class='lists-row fadeColor'>";
    HTML += "    <i class='icon-cog lists-settings fadeColor'></i>";
    HTML += "    <i class='icon-star" + (list.favorited == 1 ? "2" : "") + " lists-favorite fadeColor'></i>";
    HTML += "    <div class='lists-name'>" + list.name + "</div>";
    HTML += "    <div class='lists-desc'>";
    HTML += "        Last Updated <span class='time' data-listid='" + list.id + "'>" + timeAgo(list.last_updated_date) + "</span> ago by " + list.last_updater + ".";
    if(!list.is_owner)
        HTML += "        Owner by " + list.owner + ".";
    HTML += "    </div>";
    HTML += "</div>";
    return HTML;
}

function getItemHTML(item) {
    var HTML = "";
    HTML += "<div class='list-item' id='item-" + item.id + "' data-itemid='" + item.id + "'>";
    HTML += "    <div class='icon-check list-item-check'></div>";
    HTML += "    <div class='list-item-name'>" + item.name + "</div>";
    HTML += "    <div class='icon-close list-item-x'></div>";
    HTML += "</div>";
    return HTML;
}