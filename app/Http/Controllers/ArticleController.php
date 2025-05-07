<?php

namespace App\Http\Controllers;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        // Verifica che `paginate()` sia configurato correttamente
        $articles = Article::with('user')->orderBy('created_at', 'desc')->paginate(10);

        // Log per verificare i dati restituiti dal controller
        \Log::info('Articles paginated response:', ['articles' => $articles]);

        return response()->json($articles);
    }



    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'body' => $request->body,
            'image' => $imagePath,
        ]);

        // Log dell'articolo creato
        \Log::info('New article created:', ['article' => $article]);

        return response()->json($article, 201);
    }
    public function show($id)
    {
        // Trova l'articolo tramite ID e carica l'utente associato
        $article = Article::with('user')->findOrFail($id);

        // Restituisci una view usando Inertia, passando l'articolo alla pagina Vue
        return Inertia::render('SingleArticle', [
            'article' => $article,
        ]);
    }
    public function latest()
    {
        // Recupera i 5 articoli più recenti
        $latestArticles = Article::with('user')->orderBy('created_at', 'desc')->take(2)->get();

        // Log degli articoli più recenti
        \Log::info('Latest articles fetched:', ['latestArticles' => $latestArticles]);

        // Restituisci i dati come JSON
        return response()->json(['data' => $latestArticles]);
    }

}