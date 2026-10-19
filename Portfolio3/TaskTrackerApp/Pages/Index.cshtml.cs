using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;

namespace TaskTrackerApp.Pages;

public class IndexModel : PageModel
{
    public IActionResult OnGet() => RedirectToPage("/Tasks");
}